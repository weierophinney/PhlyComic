<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use PhpCss;

use function count;
use function file_get_contents;

class Sheldon extends AbstractComicSource
{
    use XPathTrait;

    private const SELECTOR_NEXT  = 'a#sidenav-next';
    private const SELECTOR_PREV  = 'a#sidenav-prev';
    private const SELECTOR_COMIC = '#spliced-comic span img';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'sheldon',
            'Sheldon',
            'https://www.sheldoncomics.com/',
        );
    }

    public function fetch(HttpClient $client): Comic
    {
        $comic    = self::provides();
        $response = $client->sendRequest($client->createRequest('GET', $comic->url));
        if ($response->getStatusCode() > 299) {
            return $this->registerError('Unable to reach Sheldon comic');
        }

        $page   = $response->getBody()->__toString();
        $imgUrl = $this->getUrlFromPageNode($page, self::SELECTOR_COMIC, 'data-src-img');
        if ($imgUrl === null) {
            return $this->registerError('Unable to locate Sheldon comic image');
        }

        $comicUrl = $this->getComicUrl($page);

        return null === $comicUrl
            ? $comic->withInstance($comic->url, $imgUrl)
            : $comic->withInstance($comicUrl, $imgUrl);
    }

    private function getUrlFromPageNode(string $content, string $selector, string $attribute = 'href'): ?string
    {
        $xpath   = $this->getXPathForDocument($content);
        $results = $xpath->query(PhpCss::toXpath($selector));
        if (false === $results || 0 === count($results)) {
            return null;
        }

        foreach ($results as $node) {
            if (! $node->hasAttribute($attribute)) {
                continue;
            }

            return $node->getAttribute($attribute);
        }

        return null;
    }

    private function getComicUrl(string $content): ?string
    {
        $previousUrl = $this->getUrlFromPageNode($content, self::SELECTOR_PREV);
        if ($previousUrl === null) {
            return null;
        }

        $page = file_get_contents($previousUrl);
        if ($page === false) {
            return null;
        }

        return $this->getUrlFromPageNode($page, self::SELECTOR_NEXT);
    }
}
