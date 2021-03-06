<?php

namespace PhlyComic\ComicSource;

use DOMDocument;
use DOMXPath;
use PhlyComic\Comic;
use PhpCss;
use SimpleXMLElement;

/**
 * The SMBC feed provides a link to the **page** containing the comic,
 * but the link to the comic image is buried inside the description.
 *
 * This class fetches the feed, loops through entries for the first comic,
 * pulls the entry's description element, and scrapes the HTML it contains for
 * the comic image URL.
 */
class SaturdayMorningBreakfastCereal extends AbstractRssSource
{
    protected static $comics = array(
        'smbc' => 'Saturday Morning Breakfast Cereal',
    );

    protected $comicBase      = 'http://www.smbc-comics.com/';
    protected $comicShortName = 'smbc';
    protected $domQuery       = 'img';
    protected $feedUrl        = 'http://www.smbc-comics.com/rss.php';

    protected function getDataFromFeed(SimpleXMLElement $feed)
    {
        foreach ($feed->channel->item as $latest) {
            $description = (string) $latest->description;
            $link        = (string) $latest->link;
            $image       = $this->getImageFromDescription($description, $link);

            if ($image) {
                return array(
                    'daily' => $link,
                    'image' => $image,
                );
            }
        }
        return false;
    }

    protected function getImageFromDescription($description, $url)
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML($description);
        $xpath = new DOMXPath($document);
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

        if (! $imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        return $imgUrl;
    }
}
