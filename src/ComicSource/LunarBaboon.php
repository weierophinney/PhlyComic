<?php

namespace PhlyComic\ComicSource;

class LunarBaboon extends AbstractRssSource
{
    protected static $comics = array(
        'lunarbaboon' => 'LunarBaboon',
    );

    protected $comicBase      = 'http://www.lunarbaboon.com';
    protected $comicShortName = 'lunarbaboon';
    protected $feedUrl        = 'http://www.lunarbaboon.com/comics/rss.xml';
}
