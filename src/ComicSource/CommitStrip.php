<?php

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhlyComic\Comic;
use PhlyComic\HttpClient;
use PhpCss;

class CommitStrip extends AbstractDomSource
{
    protected $dailyFormat     = 'https://www.commitstrip.com/';
    protected $domIsHtml       = true;
    protected $domQuery        = '.excerpt img';
    protected $domQueryForLink = '.excerpt a';
    protected $useComicBase    = true;

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'commitstrip',
            'CommitStrip',
            'https://www.commitstrip.com/',
        );
    }

    public function fetch(HttpClient $client): Comic
    {
        $href = $this->fetchMostRecentComicPageHrefFromLandingPage($client);
        if (null === $href) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                self::provides()->name,
            ));
        }

        $response = $client->sendRequest($client->createRequest('GET', $href));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                self::provides()->name,
            ));
        }

        $html  = $response->getBody()->__toString();
        $xpath = $this->getXPathForDocument($html);
        $found = false;
        foreach ($xpath->query(PhpCss::toXpath('.entry-content img')) as $node) {
            $found = $node;
            break;
        }

        if (! $found) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; page has unexpected structure.',
                self::provides()->name,
            ));
        }

        $image = $node->getAttribute('src');

        if (! $image) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; img tag missing.',
                self::provides()->name,
            ));
        }

        return self::provides()->withInstance($href, $image);
    }

    protected function validateImageSrc(string $src): bool
    {
        if (strstr($src, '//www.commitstrip.com/wp-content/uploads/')) {
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
            if (! preg_match('#/\d{4}/\d{2}/\d{2}/#', $href)) {
                continue;
            }

            return $href;
        }

        return self::provides()->url;
    }

    private function fetchMostRecentComicPageHrefFromLandingPage(HttpClient $client): ?string
    {
        $response = $client->sendRequest($client->createRequest('GET', self::provides()->url));
        if ($response->getStatusCode() > 299) {
            return null;
        }

        $page  = $response->getBody()->__toString();
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
