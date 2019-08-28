<?php

namespace PhlyComic\ComicSource;

use SimpleXMLElement;

class FoxTrot extends AbstractRssAndDomSource
{
    protected static $comics = array(
        'foxtrot' => 'FoxTrot',
    );

    protected $comicBase      = 'http://www.foxtrot.com';
    protected $comicShortName = 'foxtrot';
    protected $domQuery       = 'figure.wp-block-image img';
    protected $feedUrl        = 'http://www.foxtrot.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';

    protected function validateFeedItem(SimpleXMLElement $item) : bool
    {
        return true;
    }
}
