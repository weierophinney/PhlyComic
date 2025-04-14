<?php

declare(strict_types=1);

namespace PhlyComic\ComicSource;

use DOMXPath;
use PhlyComic\Comic;
use PhlyComic\HttpClient;
use Symfony\Component\CssSelector\CssSelectorConverter;

use function count;
use function date;
use function sprintf;

abstract class AbstractDomSource extends AbstractComicSource
{
    use XPathTrait;

    /** @var string sprintf() string indicating URL format */
    protected string $dailyFormat = '%s';

    /** @var string Date format as a substitution in the {@link $dailyFormat} */
    protected string $dateFormat = 'Y/m/d';

    /** @var string CSS query string describing location of image in page */
    protected string $domQuery = '';

    /** @var bool Is the DOM structure HTML? */
    protected bool $domIsHtml = false;

    /** @var string DOM attribute of img tag to use */
    protected string $domAttribute = 'src';

    /** @var bool Use the comicBase instead of the daily format to retrieve */
    protected bool $useComicBase = false;

    public function fetch(HttpClient $client): Comic
    {
        return $this->fetchComic($client, $this->getUrl());
    }

    protected function getUrl(): string
    {
        return $this->useComicBase
            ? static::provides()->url
            : sprintf($this->dailyFormat, date($this->dateFormat));
    }

    protected function validateImageSrc(string $src): bool
    {
        return true;
    }

    protected function formatImageSrc(string $src): string
    {
        return $src;
    }

    protected function getDailyUrl(string $imgUrl, DOMXPath $xpath): false|string
    {
        return false;
    }

    protected function fetchComic(HttpClient $client, string $url): Comic
    {
        $response = $client->sendRequest($client->createRequest('GET', $url));
        if ($response->getStatusCode() > 299) {
            return $this->registerError(sprintf(
                'Comic at "%s" is unreachable',
                $url
            ));
        }

        $page    = $response->getBody()->__toString();
        $xpath   = $this->getXPathForDocument($page);
        $results = $xpath->query((new CssSelectorConverter())->toXPath($this->domQuery));
        if (false === $results || ! count($results)) {
            return $this->registerError(sprintf(
                'Comic at "%s" has unparseable content',
                $url
            ));
        }

        $imgUrl = false;
        foreach ($results as $node) {
            if (! $node->hasAttribute($this->domAttribute)) {
                continue;
            }

            $src = $node->getAttribute($this->domAttribute);

            if ($this->validateImageSrc($src)) {
                $imgUrl = $this->formatImageSrc($src);
                break;
            }
        }

        if (! $imgUrl) {
            return $this->registerError(sprintf(
                'Unable to find image source in "%s"',
                $url
            ));
        }

        if (false === $dailyUrl = $this->getDailyUrl($imgUrl, $xpath)) {
            $dailyUrl = $url;
        }

        return static::provides()->withInstance($dailyUrl, $imgUrl);
    }
}
