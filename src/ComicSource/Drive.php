<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

use function preg_match;

class Drive extends AbstractDomSource
{
    protected string $domQuery   = 'div#unspliced-comic img';
    protected bool $domIsHtml    = true;
    protected bool $useComicBase = true;

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
