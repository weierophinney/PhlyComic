<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhpCss;
use RuntimeException;
use PhpCss\Exception\ParserException;
use SimpleXMLElement;

/**
 * For use with feeds that contain links to pages containing the comic, but not
 * the comic itself.
 *
 * This class fetches the feed, loops through entries for the first comic,
 * pulls the page in that entry's link element, and then scrapes the page for
 * the comic image URL.
 */
abstract class AbstractRssAndDomSource extends AbstractRssSource
{
    use XPathTrait;

    /**
     * @var string The CSS query to execute on pages pulled from the feed in
     * order to identify the image tag.
     */
    protected $domQuery       = '';

    /**
     * Which attribute of an img tag to use as the image source link.
     */
    protected string $domAttribute = 'src';

    abstract protected function validateFeedItem(SimpleXMLElement $item) : bool;

    /**
     * @return Comic|array|bool
     */
    protected function getDataFromFeed(SimpleXMLElement $feed)
    {
        foreach ($feed->channel->item as $latest) {
            $title   = (string) $latest->title;
            if (! $this->validateFeedItem($latest)) {
                continue;
            }

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
        $page = $this->fetchURL($url);
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
                'Comic at "%s" cannot be found',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($results as $node) {
            if ($node->hasAttribute($this->domAttribute)) {
                $imgUrl = $node->getAttribute($this->domAttribute);
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

    protected function fetchURL(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2');

        $html = curl_exec($ch);

        curl_close($ch);

        return $html;
    }
}
