<?php

namespace PhlyComic\ComicSource;

use DateTimeImmutable;
use DateTimeInterface;
use PhlyComic\Comic;
use SimpleXMLElement;

abstract class AbstractDateOrderedRssSource extends AbstractRssSource
{
    public function fetch()
    {
        $this->content = null;

        // Retrieve feed to parse
        $rawFeed = $this->fetchFeed($this->feedUrl);

        $sxl = $this->getXmlElement($rawFeed);
        if ($sxl instanceof Comic) {
            return $sxl;
        }

        $data = $this->getDataFromFeed($sxl);

        if ($data instanceof Comic) {
            return $data;
        }

        if (! $data) {
            return $this->registerError(sprintf(
                '%s feed does not include image description containing image URL: %s',
                static::$comics[$this->comicShortName],
                $this->content
            ));
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $data['daily'],
            /* 'image' => */ $data['image']
        );

        return $comic;
    }

    protected function getDataFromFeed(SimpleXMLElement $feed)
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
            fwrite(STDERR, "No items with images found!\n");
            return false;
        }

        usort($items, fn (array $a, array $b): int => $b['date'] <=> $a['date']);

        $item = array_shift($items);
        $this->content = $item['content'];
        return [
            'daily' => $item['daily'],
            'image' => $item['image'],
        ];
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
