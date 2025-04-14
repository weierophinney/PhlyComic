<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use SimpleXMLElement;
use Symfony\Component\CssSelector\CssSelectorConverter;

use function count;
use function sprintf;

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
    protected string $domQuery = '';

    /**
     * Which attribute of an img tag to use as the image source link.
     */
    protected string $domAttribute = 'src';

    abstract protected function validateFeedItem(SimpleXMLElement $item): bool;

    protected function getDataFromFeed(SimpleXMLElement $feed, HttpClient $client): Comic
    {
        foreach ($feed->channel->item as $latest) {
            if (! $this->validateFeedItem($latest)) {
                continue;
            }

            // Grab data from <link> element
            $link  = (string) $latest->link;
            $image = $this->getImageFromLink($client, $link);

            // If we have a Comic, it's because of an
            // error; return it directly.
            if ($image instanceof Comic) {
                return $image;
            }

            if (! empty($image)) {
                return static::provides()->withInstance($link, $image);
            }
        }

        return static::provides()->withError('Unable to find latest image');
    }

    protected function getImageFromLink(HttpClient $client, string $url): string|Comic
    {
        $response = $client->sendRequest($client->createRequest('GET', $url));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $page    = $response->getBody()->__toString();
        $xpath   = $this->getXPathForDocument($page);
        $results = $xpath->query((new CssSelectorConverter())->toXPath($this->domQuery));

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

        if (! $imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        return $imgUrl;
    }
}
