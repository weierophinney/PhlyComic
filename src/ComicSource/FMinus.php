<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class FMinus extends AbstractRssSource
{
    protected string $feedUrl = 'https://comiccaster.xyz/rss/fminus';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'fminus',
            'F Minus',
            'https://www.gocomics.com/fminus'
        );
    }
}
