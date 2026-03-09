<?php

namespace App\Support;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class SafeFileNamer extends FileNamer
{
    public function originalFileName(string $fileName): string
    {
        $baseName = parent::originalFileName($fileName);

        return $this->sanitize($baseName);
    }

    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        return $this->sanitize($baseName).'-'.$conversion->getName();
    }

    public function responsiveFileName(string $fileName): string
    {
        return $this->sanitize(pathinfo($fileName, PATHINFO_FILENAME));
    }

    private function sanitize(string $name): string
    {
        $ascii = Str::ascii($name);

        $safe = preg_replace('/[^a-zA-Z0-9._-]/', '-', $ascii);

        return preg_replace('/-+/', '-', trim($safe, '-'));
    }
}
