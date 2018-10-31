<?php

namespace PhlyComic\ComicSource;

class PhDComics extends AbstractRssSource
{
    protected static $comics = array(
        'phd' => 'PhD Comics',
    );

    protected $comicBase      = 'http://phdcomics.com';
    protected $comicShortName = 'phd';
    protected $feedUrl        = 'http://phdcomics.com/gradfeed.php';
}
