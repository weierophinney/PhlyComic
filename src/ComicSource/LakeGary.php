<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class LakeGary extends AbstractRssSource
{
    protected $feedUrl = 'http://lakegary.com/rss';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'lakegary',
            'Lake Gary',
            'http://lakegary.com/',
        );
    }
}
