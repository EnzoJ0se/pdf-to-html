<?php

namespace Enzojose\PdfToHtml;

use Enzojose\PdfToHtml\Exceptions\ExtractorNotFoundException;
use Enzojose\PdfToHtml\Exceptions\FileNotFoundException;
use Symfony\Component\Process\Process;

class PdfToHtml
{
	protected ?string $pdfPath;
	protected ?string $extractorPath = null;
    protected ?string $outputDir = __DIR__ . '/../tmp';
    protected ?string $outputName = null;
    protected int $timeout = 60;
    protected array $options = [
        '-s',
        '-noframes',
        '-hidden',
    ];

	public function __construct(?string $extractorPath = null)
	{
		if ($extractorPath) {
			$this->extractorPath = $extractorPath;
		}

		$defaultPaths = [
            '/usr/bin/pdftohtml',
            '/usr/local/bin/pdftohtml',
            '/opt/homebrew/bin/pdftohtml',
            '/opt/local/bin/pdftohtml',
            '/usr/local/bin/pdftohtml',
		];

        foreach ($defaultPaths as $path) {
            if (is_executable($path)) {
                $this->extractorPath = $path;
            }
        }
	}

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

	public function extractPDF(string $pdf): PdfToHtmlResponse
	{
        $this->outputName = 'pdf_' . uniqid();
        $this->pdfPath = $pdf;
        $response = new PdfToHtmlResponse(file: $pdf, extractor: $this->extractorPath);
        $process = new Process([
            $this->extractorPath,
            ...$this->options,
            $this->pdfPath,
            "$this->outputDir/$this->outputName.html"
        ]);

        $this->run($process, $response);

        return $response;
	}

    private function run(Process $process, PdfToHtmlResponse &$response): void
    {
        try {
            if (!$this->extractorPath) {
                throw new ExtractorNotFoundException();
            }

            if (!file_exists($this->pdfPath)) {
                throw new FileNotFoundException($this->pdfPath);
            }

            $process->setTimeout($this->timeout)->run();

            if (!$process->isSuccessful()) {
                $this->clearTmpFiles();

                throw new \Exception($process->getErrorOutput());
            }

            if (!file_exists("$this->outputDir/$this->outputName.html")) {
                $this->clearTmpFiles();

                throw new FileNotFoundException($this->pdfPath);
            }

            $response->process_output = $process->getOutput();
            $response->output = file_get_contents("$this->outputDir/$this->outputName.html");
            $this->clearTmpFiles();
        } catch (\Throwable $th) {
            $response->error = $th;
        }
    }

    private function clearTmpFiles(): void
    {
        foreach (glob("$this->outputDir/$this->outputName*") as $file) {
            if (str_contains($file, $this->outputName)) {
                unlink($file);
            }
        }
    }
}
