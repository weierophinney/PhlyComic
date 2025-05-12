<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use DateTimeImmutable;
use DOMNodeList;
use JsonException;
use PhlyComic\Comic;
use PhlyComic\HttpClient;
use Symfony\Component\CssSelector\CssSelectorConverter;

use function array_key_exists;
use function count;
use function date;
use function is_string;
use function json_decode;
use function sprintf;
use function str_contains;

use const JSON_THROW_ON_ERROR;

abstract class GoComics extends AbstractDomSource
{
    public function fetch(HttpClient $client): Comic
    {
        $response = $client->sendRequest($client->createRequest('GET', static::provides()->url));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                static::provides()->name,
            ));
        }

        $html    = $response->getBody()->__toString();
        $xpath   = $this->getXPathForDocument($html);
        $results = $xpath->query((new CssSelectorConverter())->toXPath('script[type="application/ld+json"]'));

        if (false === $results || ! count($results)) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; page has unexpected structure.',
                static::provides()->name,
            ));
        }

        $image = $this->parseScriptsForImage($results);

        if ($image === '') {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; img tag missing.',
                static::provides()->name,
            ));
        }

        return static::provides()->withInstance($this->generateLinkToCurrentStrip(), $image);
    }

    private function generateLinkToCurrentStrip(): string
    {
        return sprintf('%s/%s', static::provides()->url, date('Y/m/d'));
    }

    private function parseScriptsForImage(DOMNodeList $scripts): string
    {
        $nameIncludes = sprintf('%s - %s', static::provides()->title, (new DateTimeImmutable())->format('F j, Y'));

        foreach ($scripts as $script) {
            $json = $script->textContent;
            try {
                $document = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }

            if (! array_key_exists('@type', $document)) {
                continue;
            }

            if ($document['@type'] !== 'ImageObject') {
                continue;
            }

            // We want the first ImageObject that is NOT a feature splash
            if (
                ! array_key_exists('url', $document)
                || ! is_string($document['url'])
                || str_contains($document['url'], 'Feature_Splash_')
            ) {
                continue;
            }

            if (
                ! array_key_exists('name', $document)
                || ! is_string($document['name'])
                || ! str_contains($document['name'], $nameIncludes)
            ) {
                continue;
            }

            return $document['url'];
        }

        return '';
    }
}
