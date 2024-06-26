<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use PhlyComic\Comic;
use PhlyComic\HttpClient;

use function date;
use function sprintf;
use function strtolower;

class ForBetterOrForWorse extends AbstractComicSource
{
    protected string $dailyFormat       = 'http://fborfw.com/strip_fix/%s/%s/%s/';
    protected string $imageFormat       = 'http://fborfw.com/strip_fix/strips/fb%s.gif';
    protected string $sundayImageFormat = 'http://fborfw.com/strip_fix/strips/fb%s.jpg';

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'fborfw',
            'For Better or For Worse',
            'http://fborfw.com/',
        );
    }

    public function fetch(HttpClient $client): Comic
    {
        $format = match (strtolower(date('l'))) {
            'sunday' => $this->sundayImageFormat,
            default  => $this->imageFormat,
        };

        return self::provides()->withInstance(
            sprintf($this->dailyFormat, date('Y'), date('m'), date('d')),
            sprintf($format, date('ymd')),
        );
    }
}
