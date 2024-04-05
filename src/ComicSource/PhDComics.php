<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class PhDComics extends AbstractRssSource
{
    protected $feedUrl = 'https://phdcomics.com/gradfeed.php';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'phd',
            'PhD Comics',
            'https://phdcomics.com/',
        );
    }
}
