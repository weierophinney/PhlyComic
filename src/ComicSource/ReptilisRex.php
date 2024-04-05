<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class ReptilisRex extends AbstractRssSource
{
    protected $feedUrl = 'https://reptilisrex.com/feed/';
    protected $tagNamespace = 'content';
    protected $tagWithImage = 'encoded';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'reptilis-rex',
            'Reptilis Rex',
            'https://www.reptilisrex.com/',
        );
    }
}
