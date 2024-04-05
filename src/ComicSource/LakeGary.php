<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class LakeGary extends AbstractRssSource
{
    protected string $feedUrl = 'http://lakegary.com/rss';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'lakegary',
            'Lake Gary',
            'http://lakegary.com/',
        );
    }
}
