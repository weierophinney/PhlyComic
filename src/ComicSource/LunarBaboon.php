<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class LunarBaboon extends AbstractRssSource
{
    protected $feedUrl = 'http://www.lunarbaboon.com/comics/rss.xml';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'lunarbaboon',
            'LunarBaboon',
            'http://www.lunarbaboon.com/',
        );
    }
}
