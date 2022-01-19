<?php

namespace PhlyComic\ComicSource;

use InvalidArgumentException;
use PhlyComic\Comic;

class GoComics extends AbstractDomSource
{
    protected static $comics = array(
        'bloom-county'    => 'Bloom County 2015',
        'calvinandhobbes' => 'Calvin and Hobbes',
        'closetohome'     => 'Close to Home',
        'culdesac'        => 'Cul de Sac',
        'fminus'          => 'F Minus',
        'goats'           => 'Goats',
        'nonsequitur'     => 'Non Sequitur',
        'peanuts'         => 'Peanuts',
        'pickles'         => 'Pickles',
    );

    protected $comicFormat = 'https://www.gocomics.com/%s';
    protected $dateFormat  = 'Y/m/d';
    protected $domQuery    = 'picture.item-comic-image img';

    private $baseUrl     = 'https://www.gocomics.com';

    public function __construct($name)
    {
        if (! isset(static::$comics[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The comic "%s" is unsupported by this class',
                $name
            ));
        }
        $this->comicShortName = $name;
        $this->comicBase      = sprintf($this->comicFormat, $name);
        $this->dailyFormat    = $this->comicBase . '/%s';
    }

    public function fetch()
    {
        $href = $this->fetchMostRecentComicPageHrefFromLandingPage();
        if (! $href) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"',
                $this->comicShortName
            ));
        }

        $html = file_get_contents($href);
        $xpath = $this->getXPathForDocument($html);
        $found = false;

        foreach ($xpath->document->getElementsByTagName('picture') as $node) {
            if ($node->hasAttribute('class')
                && false !== strpos($node->getAttribute('class'), 'item-comic-image')
            ) {
                $found = $node;
                break;
            }
        }

        if (! $found) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; page has unexpected structure.',
                $this->comicShortName
            ));
        }

        $image = false;
        foreach ($found->childNodes as $node) {
            if ($node->nodeName === 'img') {
                $image = $node->getAttribute('src');
                break;
            }
        }

        if (! $image) {
            return $this->registerError(sprintf(
                'Unable to find most recent comic for "%s"; img tag missing.',
                $this->comicShortName
            ));
        }

        return new Comic(
            /* 'name'  => */ static::$comics[$this->comicShortName],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ $href,
            /* 'image' => */ $image
        );
    }

    private function fetchMostRecentComicPageHrefFromLandingPage() : ?string
    {
        $page = file_get_contents($this->comicBase);
        if (! $page) {
            return null;
        }

        $comic = null;
        $xpath = $this->getXPathForDocument($page);
        foreach ($xpath->document->getElementsByTagName('a') as $link) {
            if (! $link->hasAttribute('data-link')) {
                continue;
            }
            if ('comics' !== $link->getAttribute('data-link')) {
                continue;
            }
            return sprintf('%s%s', $this->baseUrl, $link->getAttribute('href'));
        }

        return null;
    }

    private function fetchComicFromDiscovered() : Comic
    {
    }
}
