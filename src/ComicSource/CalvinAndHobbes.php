<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class CalvinAndHobbes extends AbstractRssSource
{
    protected string $feedUrl = 'https://comiccaster.xyz/rss/calvinandhobbes';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'calvinandhobbes',
            'Calvin and Hobbes',
            'https://www.gocomics.com/calvinandhobbes'
        );
    }
}
