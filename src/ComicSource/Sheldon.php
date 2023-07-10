<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhpCss;

class Sheldon extends AbstractComicSource
{
    use XPathTrait;

    private const BASE_URL = 'http://www.sheldoncomics.com/';
    private const SELECTOR_NEXT = 'a#sidenav-next';
    private const SELECTOR_PREV = 'a#sidenav-prev';
    private const SELECTOR_COMIC = 'div#comic img';

    protected static $comics = array(
        'sheldon' => 'Sheldon',
    );

    public function fetch()
    {
        $page = file_get_contents(self::BASE_URL);
        if ($page === false) {
            return $this->registerError('Unable to reach Sheldon comic');
        }

        $imgUrl = $this->getUrlFromPageNode($page, self::SELECTOR_COMIC, 'src');
        if ($imgUrl === null) {
            return $this->registerError('Unable to locate Sheldon comic image');
        }

        $comicUrl = $this->getComicUrl($page);

        return null === $comicUrl
            ? new Comic(
                static::$comics['sheldon'],
                self::BASE_URL,
                self::BASE_URL,
                $imgUrl,
            )
            : new Comic(
                static::$comics['sheldon'],
                self::BASE_URL,
                $comicUrl,
                $imgUrl,
            );
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            static::$comics['sheldon'],
            self:: BASE_URL,
        );
        $comic->setError($message);
        return $comic;
    }

    private function getUrlFromPageNode(string $content, string $selector, string $attribute = 'href'): ?string
    {
        $xpath = $this->getXPathForDocument($content);
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
