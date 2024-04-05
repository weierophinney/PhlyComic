<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;
use SimpleXMLElement;

use function sprintf;

abstract class AbstractAtomSource extends AbstractRssSource
{
    protected function getDataFromFeed(SimpleXMLElement $feed, HttpClient $client): Comic
    {
        foreach ($feed->entry as $latest) {
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
}
