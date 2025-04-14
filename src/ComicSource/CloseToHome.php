<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class CloseToHome extends GoComics
{
    protected string $imgClass = 'Comic_comic__image__';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'closetohome',
            'Close to Home',
            'https://www.gocomics.com/closetohome'
        );
    }
}
