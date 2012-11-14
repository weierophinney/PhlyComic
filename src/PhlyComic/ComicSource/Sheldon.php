<?php

namespace PhlyComic\ComicSource;

class Sheldon extends AbstractRssSource
{
    protected static $comics = array(
        'sheldon' => 'Sheldon',
    );

    protected $comicBase      = 'http://www.sheldoncomics.com';
    protected $comicShortName = 'sheldon';
    protected $feedUrl        = 'http://www.reptilisrex.com/index.xml';
}
