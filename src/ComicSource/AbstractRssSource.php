<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use SimpleXMLElement;

use function simplexml_load_string;

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
        $rawFeed = $this->fetchFeed($this->feedUrl);

        $sxl = $this->getXmlElement($rawFeed);
        if ($sxl instanceof Comic) {
            return $sxl;
        }

        $data = $this->getDataFromFeed($sxl);

        if ($data instanceof Comic) {
            return $data;
        }

        if (!$data) {
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

    protected function getContent(SimpleXMLElement $item)
    {
        if (!$this->tagNamespace) {
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
    protected function getXmlElement(string $xml)
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

    protected function getDataFromFeed(SimpleXMLElement $feed)
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
        if (preg_match('/\<img [^>]*src="(?P<src>[^"]+)"/', $content, $matches)) {
            return $matches['src'];
        }

        fwrite(STDERR, "Unable to find img tag: $content\n");
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

    protected function fetchFeed($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1'
        );
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }

    protected function isOfInterest($item)
    {
        return true;
    }
}
