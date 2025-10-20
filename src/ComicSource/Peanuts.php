<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class Peanuts extends AbstractRssSource
{
    protected string $feedUrl = 'https://comiccaster.xyz/rss/peanuts';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'peanuts',
            'Peanuts',
            'https://www.gocomics.com/peanuts'
        );
    }
}
