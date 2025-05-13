<?php

namespace Enzojose\PdfToHtml\Exceptions;

use Exception;

class FileNotFoundException extends Exception
{
    public function __construct(?string $file)
    {
        $fileName = $file ?: 'null';

        return parent::__construct("Cannot find the file: $fileName");
    }
}
