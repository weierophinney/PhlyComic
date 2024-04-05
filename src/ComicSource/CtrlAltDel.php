<?php

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhlyComic\Comic;
use PhpCss;

class CtrlAltDel extends AbstractDomSource
{
    protected $dailyFormat     = 'http://cad-comic.com/';
    protected $domIsHtml       = true;
    protected $domQuery        = '.comicpage a img';
    protected $domQueryForLink = '.comicpage a';
    protected $useComicBase    = true;

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'ctrlaltdel',
            'Ctrl+Alt+Del',
            'http://www.cad-comic.com/',
        );
    }

    protected function validateImageSrc(string $src): bool
    {
        if (strstr($src, '//cad-comic.com/wp-content/uploads/')) {
            return true;
        }
        return false;
    }

    protected function getDailyUrl(string $imgUrl, DOMXPath $xpath): string
    {
        foreach ($xpath->query(PhpCss::toXpath($this->domQueryForLink)) as $node) {
            if (! $node->hasAttribute('href')) {
                continue;
            }

            $href = $node->getAttribute('href');
            if (! preg_match('#/comic/#', $href)) {
                continue;
            }

            return $href;
        }

        return self::provides()->url;
    }
}
