<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class ReptilisRex extends AbstractRssSource
{
    protected string $feedUrl            = 'https://reptilisrex.com/feed/';
    protected false|string $tagNamespace = 'content';
    protected string $tagWithImage       = 'encoded';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'reptilis-rex',
            'Reptilis Rex',
            'https://www.reptilisrex.com/',
        );
    }
}
