<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use Symfony\Component\CssSelector\CssSelectorConverter;

use function date;
use function sprintf;

abstract class GoComics extends AbstractDomSource
{
    /** Potential CSS queries that can match the image */
    private const QUERY_SELECTORS = [
        'div[class*=ComicViewer_comicViewer__comic] img',
        'img[class*=Comic_comic__image_isStrip__]',
    ];

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

        foreach (self::QUERY_SELECTORS as $query) {
            $results = $xpath->query((new CssSelectorConverter())->toXPath($query));
            if (false !== $results && 0 < count($results)) {
                break;
            }
        }

        if (false === $results || ! count($results)) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; page has unexpected structure.',
                static::provides()->name,
            ));
        }

        $node  = $results->item(0);
        $image = $node->getAttribute('src');

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
}
