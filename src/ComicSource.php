<?php

namespace PhlyComic;

/**
 * Describes a comic source
 */
interface ComicSource
{
    public static function provides(): Comic;
    public function fetch(): Comic;
}
