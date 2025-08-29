<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index()
    {
        $news = News::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Berita berhasil diambil',
            'data' => $news,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'required|string',
            'imageUrl' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
        $uploadResult = $this->imageUploadService->upload($request->file('imageUrl'), 'news');

        if (!$uploadResult) {
            return response()->json(['success' => false, 'message' => 'Gagal upload gambar'], 500);
        }

        $tagsArray = array_map('trim', explode(',', $request->tags));

        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'imageUrl' => $uploadResult['secure_url'],
            'image_public_id' => $uploadResult['public_id'],
            'tags' => $tagsArray,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Berita Berhasil Dibuat',
            'data' => $news,
        ], 201);
    }

    public function show(News $news)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Berita Ditemukan',
            'data' => $news,
        ], 200);
    }

    public function update(Request $request, News $news)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'tags' => 'sometimes|required|string',
            'imageUrl' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Nullable karena update ga wajib ganti gambar
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = $request->except(['imageUrl', 'tags']);

        if ($request->hasFile('imageUrl')) {
            if ($news->image_public_id) {
                $this->imageUploadService->destroy($news->image_public_id);
            }

            $uploadResult = $this->imageUploadService->upload($request->file('imageUrl'), 'news');

            if ($uploadResult) {
                $updateData['imageUrl'] = $uploadResult['secure_url'];
                $updateData['image_public_id'] = $uploadResult['public_id'];
            }
        }

        if ($request->has('tags')) {
            $updateData['tags'] = array_map('trim', explode(',', $request->tags));
        }

        $news->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Berita Berhasil Diupdate',
            'data' => $news,
        ], 200);
    }

    public function destroy(News $news)
    {
        if ($news->image_public_id) {
            $this->imageUploadService->destroy($news->image_public_id);
        }

        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita Berhasil Dihapus',
        ], 200);
    }

    public function random()
    {
        $randomNews = News::inRandomOrder()->limit(4)->get();

        return response()->json([
            'success' => true,
            'message' => '4 Berita Acak Berhasil Diambil',
            'data' => $randomNews,
        ], 200);
    }
}
