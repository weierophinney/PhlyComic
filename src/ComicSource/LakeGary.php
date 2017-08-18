<?php

namespace PhlyComic\ComicSource;

class LakeGary extends AbstractRssSource
{
    protected static $comics = array(
        'lakegary' => 'Lake Gary',
    );

    protected $comicBase      = 'http://lakegary.com/';
    protected $comicShortName = 'lakegary';
    protected $feedUrl        = 'http://lakegary.com/rss';
}
