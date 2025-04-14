<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhlyComic\Comic;
use Symfony\Component\CssSelector\CssSelectorConverter;

use function preg_match;
use function strstr;

class CtrlAltDel extends AbstractDomSource
{
    protected string $dailyFormat     = 'http://cad-comic.com/';
    protected bool $domIsHtml         = true;
    protected string $domQuery        = '.comicpage a img';
    protected string $domQueryForLink = '.comicpage a';
    protected bool $useComicBase      = true;

    public static function provides(): Comic
    {
        return Comic::createBaseComic(
            'ctrlaltdel',
            'Ctrl+Alt+Del',
            'http://www.cad-comic.com/',
        );
    }

    protected function validateImageSrc(string $src): bool
    {
        if (strstr($src, '//cad-comic.com/wp-content/uploads/')) {
            return true;
        }
        return false;
    }

    protected function getDailyUrl(string $imgUrl, DOMXPath $xpath): string
    {
        foreach ($xpath->query((new CssSelectorConverter())->toXPath($this->domQueryForLink)) as $node) {
            if (! $node->hasAttribute('href')) {
                continue;
            }

            $href = $node->getAttribute('href');
            if (! preg_match('#/comic/#', $href)) {
                continue;
            }

            return $href;
        }

        return self::provides()->url;
    }
}
