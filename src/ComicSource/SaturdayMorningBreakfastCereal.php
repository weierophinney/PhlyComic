<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use DOMDocument;
use DOMXPath;
use PhlyComic\Comic;
use PhlyComic\HttpClient;
use SimpleXMLElement;
use Symfony\Component\CssSelector\CssSelectorConverter;

use function count;
use function sprintf;

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
    protected string $domQuery = 'img';
    protected string $feedUrl  = 'https://www.smbc-comics.com/comics/rss';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'smbc',
            'Saturday Morning Breakfast Cereal',
            'https://www.smbc-comics.com/',
        );
    }

    protected function getDataFromFeed(SimpleXMLElement $feed, HttpClient $client): Comic
    {
        $comic = self::provides();

        foreach ($feed->channel->item as $latest) {
            $description = (string) $latest->description;
            $link        = (string) $latest->link;
            $image       = $this->getImageFromDescription($description, $link);

            if ($image instanceof Comic) {
                continue;
            }

            return $comic->withInstance($link, $image);
        }

        return $comic->withError('Unable to find image in feed for ' . $comic->name);
    }

    protected function getImageFromDescription(string $description, string $url): string|Comic
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML($description);
        $xpath   = new DOMXPath($document);
        $results = $xpath->query((new CssSelectorConverter())->toXPath($this->domQuery));

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
