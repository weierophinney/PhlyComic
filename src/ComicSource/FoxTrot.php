<?php

namespace PhlyComic\ComicSource;

use DateTime;
use PhlyComic\Comic;
use PhpCss;
use SimpleXMLElement;

class FoxTrot extends AbstractRssSource
{
    use XPathTrait;

    protected static $comics = array(
        'foxtrot' => 'FoxTrot',
    );

    protected $comicBase      = 'http://www.foxtrot.com';
    protected $comicShortName = 'foxtrot';
    protected $domQuery       = 'figure.wp-block-image img';
    protected $feedUrl        = 'http://www.foxtrot.com/feed/';
    protected $tagNamespace   = 'http://purl.org/rss/1.0/modules/content/';
    protected $tagWithImage   = 'encoded';

    /**
     * @return Comic|array|bool
     */
    protected function getDataFromFeed(SimpleXMLElement $feed)
    {
        foreach ($feed->channel->item as $latest) {
            $title   = (string) $latest->title;

            // Grab data from <link> element
            $link    = (string) $latest->link;
            $image   = $this->getImageFromLink($link);

            // If we have a Comic, it's because of an
            // error; return it directly.
            if ($image instanceof Comic) {
                return $image;
            }

            if ($image) {
                return array(
                    'daily' => $link,
                    'image' => $image,
                );
            }
        }
        return false;
    }

    /**
     * @return Comic|string
     */
    protected function getImageFromLink($url)
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
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($results as $node) {
            if ($node->hasAttribute('src')) {
                $imgUrl = $node->getAttribute('src');
                break;
            }
        }

        if (!$imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        return $imgUrl;
    }
}
