<?php

namespace PhlyComic\ComicSource;

use DateTime;

class FoxTrot extends AbstractRssSource
{
    protected static $comics = array(
        'foxtrot' => 'FoxTrot',
    );

    protected $comicBase      = 'http://www.foxtrot.com';
    protected $comicShortName = 'foxtrot';
    protected $feedUrl        = 'http://www.foxtrot.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';
}
