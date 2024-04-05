<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use SimpleXMLElement;

class FoxTrot extends AbstractRssAndDomSource
{
    protected string $domQuery           = 'figure.wp-block-image img';
    protected string $feedUrl            = 'https://www.foxtrot.com/feed/';
    protected false|string $tagNamespace = 'content';
    protected string $tagWithImage       = 'encoded';

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
