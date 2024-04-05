<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class FMinus extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'fminus',
            'F Minus',
            'https://www.gocomics.com/fminus'
        );
    }
}
