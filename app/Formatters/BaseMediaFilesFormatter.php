<?php

namespace App\Formatters;

abstract class BaseMediaFilesFormatter
{
    public abstract static function getResolutions(): array;
    public abstract static function resolveImageFilePath(int $entityId, string $fullName);

    public static function format($images)
    {
        $resultImages = [];
        foreach ($images as $image) {

            if (!isset($resultImages[$image->id])) {
                $resultImages[$image->id] = [
                    'id' => $image->id,
                    'name' => $image->original_name,
                    'files' => [],
                ];
            }

            $filePath = static::resolveImageFilePath($image->entity_id, $image->file_name);

            if ($image->resolution) {
                $resolutionConfig = static::getResolutions()[$image->resolution];
                $resultImages[$image->id]['files'][$resolutionConfig['prefix']] = $filePath;
            } else {
                $resultImages[$image->id]['files'][] = $filePath;
            }
        }

        return $resultImages;
    }
}
