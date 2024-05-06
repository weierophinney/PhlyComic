<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use SimpleXMLElement;

use function array_reverse;
use function sprintf;

class NotInventedHere extends AbstractAtomSource
{
    protected string $feedUrl      = 'https://notinventedhe.re/feed';
    protected string $tagWithImage = 'content';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'nih',
            'Not Invented Here',
            'https://notinventedhe.re',
        );
    }

    protected function getDataFromFeed(SimpleXMLElement $feed, HttpClient $client): Comic
    {
        foreach ($this->reverseEntries($feed) as $latest) {
            if (! $this->isOfInterest($latest)) {
                continue;
            }

            // daily is <link> element
            $daily   = (string) $latest->id;
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

    /** @return iterable<SimpleXMLElement> */
    private function reverseEntries(SimpleXMLElement $feed): iterable
    {
        $entries = [];
        foreach ($feed->entry as $entry) {
            $entries[] = $entry;
        }

        return array_reverse($entries);
    }
}
