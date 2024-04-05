<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class ListenToMe extends AbstractRssSource
{
    protected $feedUrl = 'https://feeds.feedburner.com/PLTM';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'listen-tome',
            'Please Listen to Me',
            'http://www.listen-tome.com/',
        );
    }
}
