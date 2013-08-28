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
        // Retrieve feed to parse
        $sxl = new SimpleXMLElement($this->feedUrl, 0, true);

        // Iterate <item> elements, breaking after first
        $latest = $sxl->channel->item[0];

        // daily is <link> element
        $daily = (string) $latest->link;

        $content  = $this->getContent($latest);

        // image is in content -- /src="([^"]+)"
        if (!preg_match('/src="(?P<src>[^"]+)"/', $content, $matches)) {
            return $this->registerError(sprintf(
                static::$comics[$this->comicShortName] . ' feed does not include image description containing image URL: %s',
                $content
            ));
        }
        $image = $matches['src'];

        $comic = new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $daily,
            /* 'image' => */ $image
        );

        return $comic;
    }

    protected function getContent(SimpleXMLElement $item)
    {
        if (!$this->tagNamespace) {
            return (string) $latest->{$this->tagWithImage};
        }

        $namespacedChildren = $item->children($this->tagNamespace);
        return (string) $namespacedChildren->{$this->tagWithImage};
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
