<?php

declare(strict_types=1);

namespace PhlyComic;

/**
 * Describes a comic source
 */
interface ComicSource
{
    public static function provides(): Comic;
    public function fetch(HttpClient $client): Comic;
}
