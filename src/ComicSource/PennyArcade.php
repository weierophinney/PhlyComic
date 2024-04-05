<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use SimpleXMLElement;

use function preg_match;

class PennyArcade extends AbstractRssAndDomSource
{
    protected string $domQuery = '.comic-panel img';
    protected string $feedUrl  = 'https://www.penny-arcade.com/feed';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'pennyarcade',
            'Penny Arcade',
            'http://penny-arcade.com/comic',
        );
    }

    protected function validateFeedItem(SimpleXMLElement $item): bool
    {
        return (bool) preg_match('#Comic: #', (string) $item->description);
    }
}
