<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

use function preg_match;

class DorkTower extends AbstractRssSource
{
    protected string $feedUrl            = 'http://www.dorktower.com/feed/';
    protected false|string $tagNamespace = 'content';
    protected string $tagWithImage       = 'encoded';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'dorktower',
            'Dork Tower',
            'https://www.dorktower.com/',
        );
    }

    /**
     * Override image capturing from content.
     *
     * Images have a specific format in the feed; if they do not match that
     * format, do not use them.
     *
     * @param string $content Feed item content
     * @return false|string
     */
    protected function getImageFromContent($content): string|Comic
    {
        $image = parent::getImageFromContent($content);
        if ($image instanceof Comic) {
            return $image;
        }

        if (! preg_match('#/\d{4}/\d{2}/dorktower[^.]+\.(?:jpg|jpeg|png|gif)$#i', $image)) {
            return self::provides()->withError("Could not find image tag with supported image type (found $image)");
        }

        if (! preg_match('#^https?://i', $image)) {
            $image = 'https://www.dorktower.com/' . $image;
        }

        return $image;
    }
}
