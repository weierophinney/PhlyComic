<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class CalvinAndHobbes extends GoComics
{
    protected string $imgClass = 'Comic_comic__image__';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'calvinandhobbes',
            'Calvin and Hobbes',
            'https://www.gocomics.com/calvinandhobbes'
        );
    }
}
