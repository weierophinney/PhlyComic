<?php

namespace PhlyComic\ComicSource;

class CommitStrip extends AbstractRssSource
{
    protected static $comics = array(
        'commitstrip' => 'CommitStrip',
    );

    protected $comicBase      = 'http://www.commitstrip.com/';
    protected $comicShortName = 'commitstrip';
    protected $feedUrl        = 'http://www.commitstrip.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';
}
