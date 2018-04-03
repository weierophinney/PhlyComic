<?php

namespace PhlyComic\ComicSource;

class DorkTower extends AbstractRssSource
{
    protected static $comics = array(
        'dorktower' => 'Dork Tower',
    );

    protected $comicBase      = 'http://www.dorktower.com';
    protected $comicShortName = 'dorktower';
    protected $feedUrl        = 'http://www.dorktower.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';

    /**
     * Override image capturing from content.
     *
     * Images have a specific format in the feed; if they do not match that
     * format, do not use them.
     *
     * @param string $content Feed item content
     * @return false|string
     */
    protected function getImageFromContent($content)
    {
        $image = parent::getImageFromContent($content);
        if (! preg_match('#/\d{4}/\d{2}/dorkTower\d+\.(?:jpg|jpeg|png|gif)$#i', $image)) {
            return false;
        }
        return $image;
    }
}
