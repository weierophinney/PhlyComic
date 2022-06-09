<?php

namespace PhlyComic\ComicSource;

use SimpleXMLElement;

class Drive extends AbstractRssAndDomSource
{
    protected static $comics = array(
        'drive' => 'drive',
    );

    protected $comicBase           = 'https://www.drivecomic.com';
    protected $comicShortName      = 'drive';
    protected $feedUrl             = 'https://www.drivecomic.com/comic/feed/';
    protected $tagNamespace        = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage        = 'encoded';
    protected $domQuery            = '#unspliced-comic img.size-full';
    protected string $domAttribute = 'data-src-webp';

    protected function validateFeedItem(SimpleXMLElement $item): bool
    {
        $description = (string) $item->description;
        return ! preg_match('/must be a member/', $description);
    }
}
