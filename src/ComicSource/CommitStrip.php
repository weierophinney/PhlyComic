<?php

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhlyComic\Comic;
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

    public function fetch()
    {
        $href = $this->fetchMostRecentComicPageHrefFromLandingPage();
        if (! $href) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                $this->comicShortName
            ));
        }

        $html  = file_get_contents($href);
        $xpath = $this->getXPathForDocument($html);
        $found = false;
        foreach ($xpath->query(PhpCss::toXpath('.entry-content img')) as $node) {
            $found = $node;
            break;
        }

        if (! $found) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; page has unexpected structure.',
                $this->comicShortName
            ));
        }

        $image = $node->getAttribute('src');

        if (! $image) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; img tag missing.',
                $this->comicShortName
            ));
        }

        return new Comic(
            static::$comics[$this->comicShortName],
            $this->comicBase,
            $href,
            $image
        );
    }

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

    private function fetchMostRecentComicPageHrefFromLandingPage(): ?string
    {
        $page = file_get_contents($this->comicBase);
        if (! $page) {
            return null;
        }

        $xpath = $this->getXPathForDocument($page);
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

        return null;
    }
}
