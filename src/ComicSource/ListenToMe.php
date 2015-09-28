<?php

namespace PhlyComic\ComicSource;

class ListenToMe extends AbstractRssSource
{
    protected static $comics = array(
        'listen-tome' => 'Please Listen to Me',
    );

    protected $comicBase      = 'http://www.listen-tome.com';
    protected $comicShortName = 'listen-tome';
    protected $feedUrl        = 'http://feeds.feedburner.com/PLTM';
}
