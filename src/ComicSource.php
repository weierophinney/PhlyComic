<?php

namespace PhlyComic;

use Psr\Http\Client\ClientInterface;

/**
 * Describes a comic source
 */
interface ComicSource
{
    public static function provides(): Comic;
    public function fetch(ClientInterface $client): Comic;
}
