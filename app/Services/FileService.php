<?php
namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function upload(UploadedFile $file, string $folder = 'uploads', $fileable = null): File
    {
        $disk = 'public';

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($folder, $filename, $disk);

        $fileModel = File::create([
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'path' => $path,
            'disk' => $disk,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        if ($fileable) {
            $fileModel->fileable()->associate($fileable);
            $fileModel->save();
        }

        return $fileModel;
    }

    public function delete(File $file): bool
    {
        Storage::disk($file->disk)->delete($file->path);
        return $file->delete();
    }
}