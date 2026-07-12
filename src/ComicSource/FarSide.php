<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class FarSide extends AbstractRssSource
{
    protected string $feedUrl = 'https://comiccaster.xyz/rss/farside-daily';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'farside',
            'The Far Side',
            'http://www.thefarside.com/',
        );
    }
}
