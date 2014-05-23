<?php

namespace PhlyComic\ComicSource;

class Drive extends AbstractRssSource
{
    protected static $comics = array(
        'drive' => 'drive',
    );

    protected $comicBase      = 'http://www.drivecomic.com';
    protected $comicShortName = 'drive';
    protected $feedUrl        = 'http://cdn.drivecomic.com/rss.xml';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';
}
