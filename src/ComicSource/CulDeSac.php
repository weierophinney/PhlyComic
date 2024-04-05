<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class CulDeSac extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'culdesac',
            'Cul de Sac',
            'https://www.gocomics.com/culdesac'
        );
    }
}
