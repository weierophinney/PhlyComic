<?php

namespace PhlyComic\ComicSource;

class Drive extends AbstractDomSource
{
    protected static $comics = array(
        'drive' => 'drive',
    );

    protected $comicBase           = 'https://www.drivecomic.com';
    protected $comicShortName      = 'drive';
    protected $domQuery            = 'div#unspliced-comic img';
    protected $domIsHtml           = true;
    protected $useComicBase        = true;

    protected function validateImageSrc($src): bool
    {
        return (bool) preg_match('#^https?://#', $src);
    }
}
