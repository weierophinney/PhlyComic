<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use SimpleXMLElement;

class FoxTrot extends AbstractRssAndDomSource
{
    protected $domQuery       = 'figure.wp-block-image img';
    protected $feedUrl        = 'https://www.foxtrot.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'foxtrot',
            'FoxTrot',
            'https://www.foxtrot.com/',
        );
    }

    protected function validateFeedItem(SimpleXMLElement $item): bool
    {
        return true;
    }
}
