<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class CalvinAndHobbes extends GoComics
{
    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'calvinandhobbes',
            'Calvin and Hobbes',
            'https://www.gocomics.com/calvinandhobbes'
        );
    }
}
