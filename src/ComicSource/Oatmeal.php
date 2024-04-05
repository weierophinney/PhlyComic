<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class Oatmeal extends AbstractDateOrderedRssSource
{
    protected string $feedUrl      = 'https://feeds.feedburner.com/oatmealfeed';
    protected string $tagWithImage = 'description';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'oatmeal',
            'The Oatmeal',
            'https://www.theoatmeal.com/',
        );
    }
}
