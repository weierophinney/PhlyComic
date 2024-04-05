<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class BloomCounty extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'bloom-county',
            'Bloom County 2015',
            'https://www.gocomics.com/bloom-county'
        );
    }
}
