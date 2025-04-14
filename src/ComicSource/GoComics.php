<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;

use function date;
use function sprintf;
use function str_contains;

abstract class GoComics extends AbstractDomSource
{
    protected string $domQuery = 'picture.item-comic-image img';
    protected string $imgClass = 'Comic_comic__image_strip__';

    public function fetch(HttpClient $client): Comic
    {
        $response = $client->sendRequest($client->createRequest('GET', static::provides()->url));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                static::provides()->name,
            ));
        }

        $html  = $response->getBody()->__toString();
        $dom   = $this->getDOMDocument($html);
        $found = false;

        foreach ($dom->getElementsByTagName('img') as $node) {
            if (
                $node->hasAttribute('class')
                && false !== str_contains($node->getAttribute('class'), $this->imgClass)
            ) {
                $found = $node;
                break;
            }
        }

        if (! $found) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; page has unexpected structure.',
                static::provides()->name,
            ));
        }

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
