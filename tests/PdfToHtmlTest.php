<?php

use Enzojose\PdfToHtml\Exceptions\FileNotFoundException;
use Enzojose\PdfToHtml\PdfToHtml;

test('it handle valid files', function (): void {
    $pdfToHtml = new PdfToHtml();
    $response = $pdfToHtml->extractPDF(__DIR__ . '/Files/pdf.pdf');

    expect($response->isSuccesful())->toBe(false);
    expect($response->error)->toBeInstanceOf(FileNotFoundException::class);
});

test('it can extract single page PDFs', function (): void {
    $pdfToHtml = new PdfToHtml();
    $response = $pdfToHtml->extractPDF(__DIR__ . '/Files/simple-test-document.pdf');

    expect($response->isSuccesful())->toBe(true);
    expect($response->error)->toBeNull();
    expect(strip_tags($response->output))->toBeString();
    expect(strip_tags($response->output))->toContain('Documento sem título');
});

test('it can extract multiple page PDFs', function (): void {
    $pdfToHtml = new PdfToHtml();
    $response = $pdfToHtml->extractPDF(__DIR__ . '/Files/test-document-with-many-pages.pdf');

    expect($response->isSuccesful())->toBe(true);
    expect($response->error)->toBeNull();
    expect(strip_tags($response->output))->toBeString();
    expect(strip_tags($response->output))->toContain('Documento sem título');
    expect(strip_tags($response->output))->toContain('PAGE&#160;TWO&#160;&#160;');
    expect(strip_tags($response->output))->toContain('PAGE&#160;TWO&#160;&#160;');
});
