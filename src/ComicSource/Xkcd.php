<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class Xkcd extends AbstractRssSource
{
    protected string $feedUrl = 'https://xkcd.com/rss.xml';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'xkcd',
            'XKCD',
            'https://xkcd.com/',
        );
    }
}
