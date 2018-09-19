<?php

namespace PhlyComic\ComicSource;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

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

    protected $comicFormat = 'http://www.gocomics.com/%s';
    protected $dateFormat  = 'Y/m/d';
    protected $domQuery    = 'picture.item-comic-image img';

    private $currentUrl;

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
        // Iterate over the past week of comics, attempting to find
        // a valid URL.
        foreach ($this->iterateDailyUrls() as $url) {
            $this->currentUrl = $url;

            $comic = parent::fetch();

            if ($comic && ! $comic->hasError()) {
                return $comic;
            }
        }

        return $comic ?? $this->registerError(sprintf(
            'Unable to find image source in last 7 days for "%s" at %s',
            $this->comicShortName,
            $this->comicBase
        ));
    }

    protected function getUrl() : string
    {
        return $this->currentUrl;
    }

    private function iterateDailyUrls() : iterable
    {
        $date = new DateTimeImmutable();
        $i = 0;
        do {
            yield sprintf($this->dailyFormat, $date->format($this->dateFormat));

            $i += 1;
            $interval = new DateInterval(sprintf('P%dD', $i));
            $date = $date->sub($interval);
        } while ($i < 7);
    }
}
