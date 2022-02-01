<?php

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhpCss;

class CommitStrip extends AbstractDomSource
{
    protected static $comics = array(
        'commitstrip' => 'CommitStrip',
    );

    protected $comicBase      = 'https://www.commitstrip.com/';
    protected $comicShortName = 'commitstrip';

    protected $dailyFormat     = 'https://www.commitstrip.com/';
    protected $domIsHtml       = true;
    protected $domQuery        = '.excerpt img';
    protected $domQueryForLink = '.excerpt a';
    protected $useComicBase    = true;

    protected function validateImageSrc($src)
    {
        if (strstr($src, '//www.commitstrip.com/wp-content/uploads/')) {
            return true;
        }
        return false;
    }

    protected function getDailyUrl($imgUrl, DOMXPath $xpath)
    {
        foreach ($xpath->query(PhpCss::toXpath($this->domQueryForLink)) as $node) {
            if (! $node->hasAttribute('href')) {
                continue;
            }

            $href = $node->getAttribute('href');
            if (! preg_match('#/\d{4}/\d{2}/\d{2}/#', $href)) {
                continue;
            }

            return $href;
        }
        return $this->comicBase;
    }
}
