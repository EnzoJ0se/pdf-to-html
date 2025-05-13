<?php

namespace Enzojose\PdfToHtml\Exceptions;

use Exception;

class ExtractorNotFoundException extends Exception
{
    public function __construct()
    {
        return parent::__construct('Cant extract the PDF without the [extractor] binary file.');
    }
}
