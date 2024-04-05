<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use DOMDocument;
use DOMElement;
use DOMXPath;
use PhlyComic\HttpClient;

class ScenesFromAMultiverse extends AbstractComicSource
{
    protected $feedUrl   = 'http://feeds.feedburner.com/ScenesFromAMultiverse';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'sfam',
            'Scenes From A Multiverse',
            'http://amultiverse.com/',
        );
    }

    public function fetch(HttpClient $client): Comic
    {
        $response = $client->sendRequest($client->createRequest('GET', $this->feedUrl));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf('Unable to fetch feed %s', $this->feedUrl));
        }

        $feed = $response->getBody()->__toString();
        $sxl  = simplexml_load_string($feed, options: LIBXML_NOCDATA);

        // Iterate <item> elements, breaking after first
        $latest = $sxl->channel->item[0];

        // daily is <guid> element
        $daily = (string) $latest->guid;

        // Parse description
        $desc   = (string) $latest->description;
        $dom    = new DOMDocument();
        $dom->loadHTML($desc);

        $xpath  = new DOMXPath($dom);
        $result = $xpath->query('//a/img');
        if (!$result || !$result->length) {
            return $this->registerError(sprintf(
                'Unable to find Scenes From A Multiverse comic image in description ("%s")',
                $desc
            ));
        }

        /** @var DOMElement $img */
        $img = $result->item(0);

        if (! $img->hasAttribute('src')) {
            return $this->registerError(sprintf(
                'Scenes From A Multiverse image does not contain a src attribute: %s',
                $desc
            ));
        }
        $image = $img->getAttribute('src');

        return self::provides()->withInstance($daily, $image);
    }
}
