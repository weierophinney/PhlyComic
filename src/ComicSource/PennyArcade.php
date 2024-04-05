<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use SimpleXMLElement;

class PennyArcade extends AbstractRssAndDomSource
{
    protected $domQuery = '.comic-panel img';
    protected $feedUrl  = 'https://www.penny-arcade.com/feed';

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
