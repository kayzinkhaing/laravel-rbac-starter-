<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

trait HasMedia
{
    /**
     * Save new media files.
     */
    public function saveMedia($model, $mediaData)
    {
        $mediaData = is_array($mediaData) ? $mediaData : [$mediaData];

        foreach ($mediaData as $file) {
            if (! ($file instanceof UploadedFile)) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType() ?? 'application/octet-stream';
            $size = $file->getSize() ?? 0;
            $path = $file->store('uploads', 'private');

            $model->media()->create([
                'url' => $path,
                'original_filename' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size,
                'uploaded_at' => now(),
            ]);
        }
    }

    /**
     * Update existing media (replace old file with new file)
     */
    public function updateMedia($model, UploadedFile $file)
    {
        $mediaRecord = $model->media()->first();
        $previousPath = $mediaRecord?->url;
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize() ?? 0;
        $path = $file->store('uploads', 'private');

        try {
            $attributes = [
                'url' => $path,
                'original_filename' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size,
                'uploaded_at' => now(),
            ];

            if ($mediaRecord) {
                $mediaRecord->update($attributes);
            } else {
                $model->media()->create($attributes);
            }
        } catch (Throwable $exception) {
            Storage::disk('private')->delete($path);
            throw $exception;
        }

        if ($previousPath) {
            Storage::disk('private')->delete($previousPath);
        }
    }

    /**
     * Delete all media related to a model
     */
    public function deleteMedia($model)
    {
        $mediaRecords = $model->media()->get();

        foreach ($mediaRecords as $media) {
            Storage::disk('private')->delete($media->url);
        }

        $model->media()->delete();
    }
}
