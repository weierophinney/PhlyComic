<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use DOMDocument;
use DOMXPath;
use RuntimeException;

use function libxml_get_errors;
use function libxml_use_internal_errors;
use function sprintf;
use function var_export;

trait XPathTrait
{
    protected function getXPathForDocument(string $content): DOMXPath
    {
        $document = $this->getDOMDocument($content);

        return new DOMXPath($document);
    }

    protected function getDOMDocument(string $content): DOMDocument
    {
        libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $success  = $document->loadHTML($content);

        $errors = libxml_get_errors();
        libxml_use_internal_errors(false);

        if (! $success) {
            throw new RuntimeException(sprintf(
                'Unable to parse HTML: %s',
                var_export($errors, true)
            ));
        }

        return $document;
    }
}
