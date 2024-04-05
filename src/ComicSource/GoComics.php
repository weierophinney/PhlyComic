<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;

abstract class GoComics extends AbstractDomSource
{
    protected $domQuery = 'picture.item-comic-image img';
    private $baseUrl    = 'https://www.gocomics.com';

    public function fetch(HttpClient $client): Comic
    {
        $href = $this->fetchMostRecentComicPageHrefFromLandingPage($client);
        if (null === $href) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                static::provides()->name,
            ));
        }

        $response = $client->sendRequest($client->createRequest('GET', $href));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                static::provides()->name,
            ));
        }

        $html  = $response->getBody()->__toString();
        $xpath = $this->getXPathForDocument($html);
        $found = false;

        foreach ($xpath->document->getElementsByTagName('picture') as $node) {
            if ($node->hasAttribute('class')
                && false !== strpos($node->getAttribute('class'), 'item-comic-image')
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

        $image = false;
        foreach ($found->childNodes as $node) {
            if ($node->nodeName === 'img') {
                $image = $node->getAttribute('src');
                break;
            }
        }

        if (! $image) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; img tag missing.',
                static::provides()->name,
            ));
        }

        return static::provides()->withInstance($href, $image);
    }

    private function fetchMostRecentComicPageHrefFromLandingPage(HttpClient $client) : ?string
    {
        $response = $client->sendRequest($client->createRequest('GET', static::provides()->url));
        if ($response->getStatusCode() > 299) {
            return null;
        }

        $page  = $response->getBody()->__toString();
        $xpath = $this->getXPathForDocument($page);
        foreach ($xpath->document->getElementsByTagName('a') as $link) {
            if (! $link->hasAttribute('data-link')) {
                continue;
            }
            if ('comics' !== $link->getAttribute('data-link')) {
                continue;
            }
            return sprintf('%s%s', $this->baseUrl, $link->getAttribute('href'));
        }

        return null;
    }
}
