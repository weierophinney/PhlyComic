<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use SimpleXMLElement;

abstract class AbstractRssSource extends AbstractComicSource
{
    /**
     * @var string Base URL to landing page for comic
     */
    protected $comicBase;

    /**
     * @var string Short name of comic
     */
    protected $comicShortName;

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

    public function fetch()
    {
        $this->content = null;

        // Retrieve feed to parse
        $sxl = new SimpleXMLElement($this->feedUrl, 0, true);

        $data = $this->getDataFromFeed($sxl);

        if (!$data) {
            return $this->registerError(sprintf(
                static::$comics[$this->comicShortName] . ' feed does not include image description containing image URL: %s',
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

    protected function getContent(SimpleXMLElement $item)
    {
        if (!$this->tagNamespace) {
            return (string) $item->{$this->tagWithImage};
        }

        $namespacedChildren = $item->children($this->tagNamespace);
        return (string) $namespacedChildren->{$this->tagWithImage};
    }

    protected function getDataFromFeed(SimpleXMLElement $feed)
    {
        foreach ($feed->channel->item as $latest) {
            // daily is <link> element
            $daily   = (string) $latest->link;
            $content = $this->getContent($latest);
            $image   = $this->getImageFromContent($content);
            if ($image) {
                return array(
                    'daily' => $daily,
                    'image' => $image,
                );
            }

            // First item seeds content
            if (null === $this->content) {
                $this->content = $content;
            }
        }
        return false;
    }

    protected function getImageFromContent($content)
    {
        // image is in content -- /src="([^"]+)"
        if (preg_match('/src="(?P<src>[^"]+)"/', $content, $matches)) {
            return $matches['src'];
        }
        return false;
    }

    protected function registerError($message)
    {
        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase
        );
        $comic->setError($message);
        return $comic;
    }
}