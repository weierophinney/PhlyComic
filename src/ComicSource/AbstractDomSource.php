<?php

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhlyComic\Comic;
use PhpCss;

abstract class AbstractDomSource extends AbstractComicSource
{
    use XPathTrait;

    /**
     * @var string URL to comic landing page
     */
    protected $comicBase;

    /**
     * @var string short name of comic
     */
    protected $comicShortName;

    /**
     * @var string sprintf() string indicating URL format
     */
    protected $dailyFormat = '%s';

    /**
     * @var string Date format as a substitution in the {@link $dailyFormat}
     */
    protected $dateFormat = 'Y/m/d';

    /**
     * @var string CSS query string describing location of image in page
     */
    protected $domQuery = '';

    /**
     * @var bool Is the DOM structure HTML?
     */
    protected $domIsHtml = false;

    /**
     * @var bool Use the comicBase instead of the daily format to retrieve
     */
    protected $useComicBase = false;

    public function fetch()
    {
        return $this->fetchComic($this->getUrl());
    }

    protected function getUrl() : string
    {
        return $this->useComicBase
            ? $this->comicBase
            : sprintf($this->dailyFormat, date($this->dateFormat));
    }

    protected function validateImageSrc($src)
    {
        return true;
    }

    protected function formatImageSrc($src)
    {
        return $src;
    }

    protected function getDailyUrl($imgUrl, DOMXPath $xpath)
    {
        return false;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }

    protected function fetchComic(string $url) : Comic
    {
        $page = file_get_contents($url);
        if (! $page) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $xpath = $this->getXPathForDocument($page);
        $results = $xpath->query(PhpCss::toXpath($this->domQuery));
        if (false === $results || ! count($results)) {
            return $this->registerError(sprintf(
                'Comic at "%s" has unparseable content',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($results as $node) {
            if (! $node->hasAttribute('src')) {
                continue;
            }

            $src = $node->getAttribute('src');

            if ($this->validateImageSrc($src)) {
                $imgUrl = $this->formatImageSrc($src);
                break;
            }
        }

        if (! $imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        if (! ($dailyUrl = $this->getDailyUrl($imgUrl, $xpath))) {
            $dailyUrl = $url;
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $dailyUrl,
            /* 'image' => */ $imgUrl
        );

        return $comic;
    }
}
