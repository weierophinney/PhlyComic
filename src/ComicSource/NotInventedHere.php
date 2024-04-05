<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class NotInventedHere extends AbstractAtomSource
{
    protected $feedUrl      = 'https://notinventedhe.re/feed';
    protected $tagWithImage = 'content';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'nih',
            'Not Invented Here',
            'https://notinventedhe.re',
        );
    }
}
