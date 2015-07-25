<?php

namespace PhlyComic\ComicSource;

class Dilbert extends AbstractDomSource
{
    protected static $comics = array(
        'dilbert' => 'Dilbert',
    );

    protected $comicBase      = 'http://dilbert.com';
    protected $comicShortName = 'dilbert';
    protected $dailyFormat    = 'http://dilbert.com/strip/%s';
    protected $dateFormat     = 'Y-m-d';
    protected $domQuery       = 'div.img-comic-container img';
}
