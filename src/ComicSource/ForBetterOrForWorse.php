<?php

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;

class ForBetterOrForWorse extends AbstractComicSource
{
    protected static $comics = array(
        'fborfw' => 'For Better or For Worse',
    );

    protected $comicBase = 'http://fborfw.com';

    protected $dailyFormat = 'http://fborfw.com/strip_fix/%s/%s/%s/';
    protected $imageFormat = 'http://fborfw.com/strip_fix/strips/fb%s.gif';
    protected $sundayImageFormat = 'http://fborfw.com/strip_fix/strips/fb%s.jpg';

    public function fetch()
    {
        switch (strtolower(date('l'))) {
            case 'sunday':
                $format = $this->sundayImageFormat;
                break;
            default:
                $format = $this->imageFormat;
                break;
        }

        $comic = new Comic(
            /* 'name'  => */ static::$comics['fborfw'],
            /* 'link'  => */ $this->comicBase,
            /* 'daily' => */ sprintf($this->dailyFormat, date('Y'), date('m'), date('d')),
            /* 'image' => */ sprintf($format, date('ymd'))
        );
        return $comic;
    }
}
