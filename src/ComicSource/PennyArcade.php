<?php

namespace PhlyComic\ComicSource;

use SimpleXMLElement;

class PennyArcade extends AbstractRssAndDomSource
{
    protected static $comics = array(
        'pennyarcade' => 'Penny Arcade',
    );

    protected $comicBase      = 'http://penny-arcade.com/comic';
    protected $comicShortName = 'pennyarcade';
    protected $domQuery       = '#comicFrame img';
    protected $feedUrl        = 'http://penny-arcade.com/feed';

    protected function validateFeedItem(SimpleXMLElement $item) : bool
    {
        return (bool) preg_match('#^Comic: #', (string) $item->title);
    }
}
