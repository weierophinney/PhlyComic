<?php

namespace PhlyComic\ComicSource;

class DorkTower extends AbstractRssSource
{
    protected static $comics = array(
        'dorktower' => 'Dork Tower',
    );

    protected $comicBase      = 'http://www.dorktower.com';
    protected $comicShortName = 'dorktower';
    protected $feedUrl        = 'http://www.dorktower.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';
}
