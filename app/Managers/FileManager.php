<?php

namespace App\Managers;

use App\Exceptions\FileNotFoundException;
use App\Managers\BaseManager;

class FileManager extends BaseManager
{
    /**
     * Checks if a given file exists
     * @param  string $filename
     * @return void
     */
    public function checkFile($filename): void
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException('"'.$filename.'" could not be found');
        }
    }
}
