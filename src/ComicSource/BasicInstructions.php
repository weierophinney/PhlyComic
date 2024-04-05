<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class BasicInstructions extends AbstractRssSource
{
    protected $feedUrl = 'https://www.basicinstructions.net/basic-instructions?format=rss';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'basicinstructions',
            'Basic Instructions',
            'http://basicinstructions.net/basic-instructions/'
        );
    }
}
