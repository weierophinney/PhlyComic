<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\ComicSource;

/**
 * Provides shared functionality for most comic source classes
 */
abstract class AbstractComicSource implements ComicSource
{
    protected function registerError(string $message): Comic
    {
        return static::provides()->withError($message);
    }
}
