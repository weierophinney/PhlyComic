<?php

namespace PhlyComic\ComicSource;

class Drive extends AbstractDomSource
{
    protected static $comics = array(
        'drive' => 'drive',
    );

    protected $comicBase           = 'https://www.drivecomic.com';
    protected $comicShortName      = 'drive';
    protected $domQuery            = 'div#spliced-comic img.size-full';
    protected $domAttribute        = 'data-src-webp';
    protected $domIsHtml           = true;
    protected $useComicBase        = true;
}
