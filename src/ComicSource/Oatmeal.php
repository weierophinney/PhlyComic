<?php

namespace PhlyComic\ComicSource;

class Oatmeal extends AbstractDateOrderedRssSource
{
    protected static $comics = array(
        'oatmeal' => 'The Oatmeal',
    );

    protected $comicBase      = 'https://www.theoatmeal.com';
    protected $comicShortName = 'oatmeal';
    protected $feedUrl        = 'https://feeds.feedburner.com/oatmealfeed';
    protected $tagWithImage   = 'description';
}
