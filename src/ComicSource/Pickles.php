<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class Pickles extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'pickles',
            'Pickles',
            'https://www.gocomics.com/pickles'
        );
    }
}
