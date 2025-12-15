<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Club;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $club = Club::where('user_id', $user->id)->first();

        return response()->json([
            'name' => $club ? $club->name : null,
            'username' => $user->username,
            'club' => $club ? [
                'logo_path' => $club->logo_path,
                'description' => $club->description,
                'group_link' => $club->group_link,
            ] : null,
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group_link' => 'nullable|url',
            'password' => 'nullable|string|min:6',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $club = Club::where('user_id', $user->id)->first();

        if (!$club) {
            return response()->json([
                'message' => 'Club tidak ditemukan untuk user ini.'
            ], 404);
        }

        $club->name = $request->name;
        $club->description = $request->description ?? $club->description;
        $club->group_link = $request->group_link ?? $club->group_link;

        if ($request->hasFile('logo')) {
            if ($club->logo_path && Storage::disk('public')->exists($club->logo_path)) {
                Storage::disk('public')->delete($club->logo_path);
            }

            $file = $request->file('logo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('storage/logos');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            $file->move($destinationPath, $filename);
            $club->logo_path = 'logos/' . $filename;
        }

        $club->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
            'club' => $club->fresh()->makeHidden(['created_at', 'updated_at']),
        ]);
    }

    public function dashboardStats() {
        $today = Carbon::today();
        $dayOfWeek = $today->locale('id')->dayName;

        $totalClubs = Club::count();

        $totalMembers = DB::table('club_student')->count();

        $todayAttendance = Attendance::whereDate('date', $today)->where('status', 'hadir')->count();

        $clubsWithScheduleToday = Schedule::where('day_of_week', $dayOfWeek)->pluck('club_id');

        $clubWithAttendanceToday = Attendance::whereDate('date', $today)->pluck('club_id')->unique();

        $violatingClubIds = $clubsWithScheduleToday->diff($clubWithAttendanceToday);
        $violatingClubs = Club::whereIn('id', $violatingClubIds)->get(['id', 'name','logo_path']);

        $incompleteClubs = Club::where(function ($query) {
            $query->whereNull('description')
                  ->orWhere('description', '')
                  ->orWhereNull('group_link')
                  ->orWhere('group_link', '');
        })
        ->orWhereDoesntHave('schedules')
        ->get(['id', 'name', 'logo_path']);

        return response()->json([
            'totalClubs' => $totalClubs,
            'totalMembers' => $totalMembers,
            'todayAttendance' => $todayAttendance,
            'violationCount' => $violatingClubs->count(),
            'violatingClubs' => $violatingClubs,
            'incompleteClubs' => $incompleteClubs,
        ]);
    }
}
