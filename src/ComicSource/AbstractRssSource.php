<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
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
        $response = $this->fetchFeed($client, $this->feedUrl);
        if (! $response->getStatusCode() === 200) {
            return $comic->withError('Unable to fetch feed');
        }
        $rawFeed = (string) $response->getBody();

        $sxl = $this->getXmlElement($rawFeed);
        if ($sxl instanceof Comic) {
            return $sxl;
        }

        return $this->getDataFromFeed($sxl);
    }

    protected function getContent(SimpleXMLElement $item): string
    {
        if (! $this->tagNamespace) {
            return (string) $item->{$this->tagWithImage};
        }

        $namespacedChildren = $item->children($this->tagNamespace);
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
                static::$comics[$this->comicShortName],
                $this->content
            ));
        }

        return $feed;
    }

    protected function getDataFromFeed(SimpleXMLElement $feed): Comic
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

            if ($image) {
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

    protected function getImageFromContent($content): string|Comic
    {
        // image is in content -- /src="([^"]+)"
        if (preg_match('/\<img [^>]*src="(?P<src>[^"]+)"/', $content, $matches)) {
            return $matches['src'];
        }

        return static::provides()->withError("Unable to find img tag: $content");
    }

    protected function registerError($message)
    {
        return static::provides()->withError($message);
    }

    protected function fetchFeed(HttpClient $client, string $url): ResponseInterface
    {
        $request = $client
            ->createRequest('GET', $url)
            ->withHeader('User-Agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1');
        return $client->sendRequest($request);
    }

    protected function isOfInterest($item)
    {
        return true;
    }
}
