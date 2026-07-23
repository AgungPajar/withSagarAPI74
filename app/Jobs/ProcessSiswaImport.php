<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\User;
use App\Models\Student;
use Exception;

class ProcessSiswaImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $kelasId;
    protected $importId;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $kelasId, $importId)
    {
        $this->filePath = $filePath;
        $this->kelasId = $kelasId;
        $this->importId = $importId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $spreadsheet = IOFactory::load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $totalRows = count($rows) - 1; // subtract header
            if ($totalRows <= 0) {
                $this->updateStatus('completed', 0, 0);
                return;
            }

            $this->updateStatus('processing', 0, $totalRows);

            $processedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index == 0) continue; // Skip header

                $nisn = $row[0] ?? null;
                $name = $row[1] ?? null;

                if ($nisn && $name) {
                    $user = User::where('username', $nisn)->first();
                    if (!$user) {
                        $user = User::create([
                            'username' => $nisn,
                            'name' => $name,
                            'role' => 'student',
                            'password' => bcrypt('ossagar123'),
                        ]);
                    } else {
                        $user->update([
                            'name' => $name,
                            'role' => 'student',
                        ]);
                    }

                    Student::updateOrCreate(
                        ['nisn' => $nisn],
                        [
                            'name' => $name,
                            'kelas_id' => $this->kelasId,
                            'user_id' => $user->id,
                        ]
                    );
                    $processedCount++;
                }

                // Update progress every 5 rows or on the last row
                if ($processedCount % 5 === 0 || $index === $totalRows) {
                    $this->updateStatus('processing', $processedCount, $totalRows);
                }
            }

            $this->updateStatus('completed', $processedCount, $totalRows);

            // Clean up the file
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

        } catch (\Throwable $e) {
            $this->updateStatus('failed', 0, 0, $e->getMessage());
        }
    }

    protected function updateStatus($status, $processed, $total, $error = null)
    {
        Cache::put('import_status_' . $this->importId, [
            'status' => $status,
            'processed' => $processed,
            'total' => $total,
            'error' => $error,
            'updated_at' => now()->timestamp,
        ], now()->addHours(2));
    }
}
