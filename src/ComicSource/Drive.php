<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class Drive extends AbstractDomSource
{
    protected $domQuery            = 'div#unspliced-comic img';
    protected $domIsHtml           = true;
    protected $useComicBase        = true;

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'drive',
            'drive',
            'https://www.drivecomic.com/',
        );
    }

    protected function validateImageSrc(string $src): bool
    {
        return (bool) preg_match('#^https?://#', $src);
    }
}
