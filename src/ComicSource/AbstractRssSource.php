<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use SimpleXMLElement;

use function simplexml_load_string;

abstract class AbstractRssSource extends AbstractComicSource
{
    /**
     * @var null|string Content of most recent item investigated in feed
     */
    protected $content;

    /**
     * @var string URI to a feed
     */
    protected $feedUrl;

    /**
     * The namespace of the tag that holds the image, if any
     *
     * @var false|string
     */
    protected $tagNamespace = false;

    /**
     * What tag in the feed contains the image?
     *
     * @var string
     */
    protected $tagWithImage = 'description';

    public function fetch(HttpClient $client): Comic
    {
        $comic = static::provides();
        $this->content = null;

        // Retrieve feed to parse
        $response = $client->sendRequest($client->createRequest('GET', $this->feedUrl));
        if ($response->getStatusCode() > 299) {
            return $comic->withError('Unable to fetch feed');
        }
        $rawFeed = (string) $response->getBody();

        $sxl = $this->getXmlElement($rawFeed);
        if ($sxl instanceof Comic) {
            return $sxl;
        }

        return $this->getDataFromFeed($sxl, $client);
    }

    protected function getContent(SimpleXMLElement $item): string
    {
        if (! $this->tagNamespace) {
            return (string) $item->{$this->tagWithImage};
        }

        $namespacedChildren = $item->children($this->tagNamespace, true);
        return (string) $namespacedChildren->{$this->tagWithImage};
    }

    /**
     * @return SimpleXMLElement|Comic Returns comic representing an error if an
     *     error occurs during parsing; otherwise, returns the SimpleXMLElement
     *     representing the content.
     */
    protected function getXmlElement(string $xml): SimpleXMLElement|Comic
    {
        $feed = simplexml_load_string($xml);
        if ($feed === false) {
            return $this->registerError(sprintf(
                '%s feed cannot be parsed',
                static::provides()->name,
                $this->content
            ));
        }

        return $feed;
    }

    protected function getDataFromFeed(SimpleXMLElement $feed, HttpClient $client): Comic
    {
        foreach ($feed->channel->item as $latest) {
            if (! $this->isOfInterest($latest)) {
                continue;
            }

            // daily is <link> element
            $daily   = (string) $latest->link;
            $content = $this->getContent($latest);
            $image   = $this->getImageFromContent($content);

            if ($image instanceof Comic) {
                return $image;
            }

            if (! empty($image)) {
                return static::provides()->withInstance($daily, $image);
            }

            // First item seeds content
            if (null === $this->content) {
                $this->content = $content;
            }
        }

        $comic = static::provides();
        return $comic->withError(sprintf(
            '%s feed does not include image description containing image URL: %s',
            $comic->name,
            $this->content,
        ));
    }

    protected function getImageFromContent(string $content): string|Comic
    {
        // image is in content -- /src="([^"]+)"
        if (preg_match('/\<img [^>]*src="(?P<src>[^"]+)"/u', $content, $matches)) {
            return $matches['src'];
        }

        return static::provides()->withError("Unable to find img tag: $content");
    }

    protected function isOfInterest(SimpleXMLElement|string $item): bool
    {
        return true;
    }
}
