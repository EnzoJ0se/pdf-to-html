<?php

namespace Enzojose\PdfToHtml;

use Throwable;

class PdfToHtmlResponse
{
    public function __construct(
        public string $file,
        public ?string $extractor = null,
        public ?string $output = null,
        public ?string $process_output = null,
        public ?Throwable $error = null,
    ) {
    }

    public function isSuccesful(): bool
    {
        return !$this->error && !!$this->output;
    }
}
