<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileReader;

class File
{
    private $file;

    public function __construct(string $requestParamName)
    {
        if (Input::hasFile($requestParamName) == false) {
            throw new \RuntimeException('No File');
        }

        $this->file = Input::file($requestParamName);

        return $this;
    }

    public function saveInStorage(string $fileName)
    {
        $name = 'storage/' . $fileName . '_' . time() . '.' . $this->file->getClientOriginalExtension();
        $storageType = env('FILES_STORAGE_DRIVER');

        Storage::disk($storageType)->put($name, FileReader::get($this->file->getRealPath()));

        return Storage::disk($storageType)->url($name);
    }
}
