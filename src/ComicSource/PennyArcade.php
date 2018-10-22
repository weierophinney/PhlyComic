<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhpCss;
use SimpleXMLElement;

/**
 * The Penny Arcade feed provides a link to the **page** containing the comic,
 * but not a link to the comic image itself.
 *
 * Additionally, the feed contains both news items as well as comics.
 *
 * This class fetches the feed, loops through entries for the first comic,
 * pulls the page in that entry's link element, and then scrapes the page for
 * the comic image URL.
 */
class PennyArcade extends AbstractRssSource
{
    use XPathTrait;

    protected static $comics = array(
        'pennyarcade' => 'Penny Arcade',
    );

    protected $comicBase      = 'http://penny-arcade.com/comic';
    protected $comicShortName = 'pennyarcade';
    protected $domQuery       = '#comicFrame img';
    protected $feedUrl        = 'http://penny-arcade.com/feed';

    protected function getDataFromFeed(SimpleXMLElement $feed)
    {
        foreach ($feed->channel->item as $latest) {
            $title   = (string) $latest->title;
            if (!preg_match('#^Comic: #', $title)) {
                continue;
            }

            // daily is <link> element
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

    protected function getImageFromLink($url)
    {
        $page = file_get_contents($url);
        if (!$page) {
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
