<?php

namespace PhlyComic\ComicSource;

use Zend\Dom\Query as DomQuery;

class CtrlAltDel extends AbstractDomSource
{
    protected static $comics = array(
        'ctrlaltdel' => 'Ctrl+Alt+Del',
    );

    protected $comicBase       = 'http://cad-comic.com/';
    protected $comicShortName  = 'ctrlaltdel';
    protected $dailyFormat     = 'http://cad-comic.com/';
    protected $domIsHtml       = true;
    protected $domQuery        = '.comicpage a img';
    protected $domQueryForLink = '.comicpage a';
    protected $useComicBase    = true;

    protected function validateImageSrc($src)
    {
        if (strstr($src, '//cad-comic.com/wp-content/uploads/')) {
            return true;
        }
        return false;
    }

    protected function getDailyUrl($imgUrl, DomQuery $dom)
    {
        foreach ($dom->execute($this->domQueryForLink) as $node) {
            if (! $node->hasAttribute('href')) {
                continue;
            }

            $href = $node->getAttribute('href');
            if (! preg_match('#/comic/#', $href)) {
                continue;
            }

            return $href;
        }
        return $this->comicBase;
    }
}
