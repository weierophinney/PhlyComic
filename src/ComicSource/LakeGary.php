<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class LakeGary extends AbstractRssSource
{
    protected string $feedUrl = 'https://lakegary.com/feed/';

    protected string $tagWithImage = 'encoded';
    protected false|string $tagNamespace = 'content';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'lakegary',
            'Lake Gary',
            'https://lakegary.com/',
        );
    }
}
