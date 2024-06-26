<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class GarfieldMinusGarfield extends AbstractRssSource
{
    protected string $feedUrl = 'http://garfieldminusgarfield.net/rss';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'g-g',
            'Garfield Minus Garfield',
            'http://garfieldminusgarfield.net/',
        );
    }
}
