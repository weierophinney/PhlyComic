<?php

namespace PhlyComic\ComicSource;

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
    protected $domQuery    = 'img.strip';

    public function __construct($name)
    {
        if (!isset(static::$comics[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The comic "%s" is unsupported by this class',
                $name
            ));
        }
        $this->comicShortName = $name;
        $this->comicBase      = sprintf($this->comicFormat, $name);
        $this->dailyFormat    = $this->comicBase . '/%s';
    }
}
