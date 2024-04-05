<?php

namespace PhlyComic\ComicSource;

use DateTimeImmutable;
use DateTimeInterface;
use PhlyComic\Comic;
use PhlyComic\HttpClient;
use SimpleXMLElement;

abstract class AbstractDateOrderedRssSource extends AbstractRssSource
{
    public function fetch(HttpClient $client): Comic
    {
        $this->content = null;

        // Retrieve feed to parse
        $response = $client->sendRequest($client->createRequest('GET', $this->feedUrl));
        if ($response->getStatusCode() > 299) {
            return static::provides()->withError('Unable to fetch feed at ' . $this->feedUrl);
        }

        $rawFeed = $response->getBody()->__toString();
        $sxl     = $this->getXmlElement($rawFeed);
        if ($sxl instanceof Comic) {
            return $sxl;
        }

        return $this->getDataFromFeed($sxl, $client);

    }

    protected function getDataFromFeed(SimpleXMLElement $feed, HttpClient $client): Comic
    {
        $items = [];
        foreach ($feed->item as $item) {
            if (! $this->isOfInterest($item)) {
                fwrite(STDERR, "Item is not of interest?\n");
                continue;
            }

            $date = $this->getItemDate($item);
            if (null === $date) {
                fwrite(STDERR, "No associated date for item: . " . $item->asXML() . "\n");
                continue;
            }

            $content = $this->getContent($item);
            $image   = $this->getImageFromContent($content);

            if (! $image) {
                fwrite(STDERR, "No associated image for item: . " . $item->asXML() . "\n");
                continue;
            }

            $items[] = [
                'daily'   => (string) $item->link,
                'date'    => $date,
                'image'   => $image,
                'content' => $content,
            ];
        }

        if ($items === []) {
            return $this->registerError(sprintf(
                '%s feed does not include image description containing image URL: %s',
                static::provides()->name,
                $this->content
            ));
        }

        usort($items, fn (array $a, array $b): int => $b['date'] <=> $a['date']);

        $item = array_shift($items);
        $this->content = $item['content'];

        return static::provides()->withInstance($item['daily'], $item['image']);
    }

    protected function getItemDate(SimpleXMLElement $item): ?DateTimeInterface
    {
        $children = $item->children('dc', true);
        if (! $children->date) {
            return null;
        }

        return new DateTimeImmutable((string) $children->date);
    }
}
