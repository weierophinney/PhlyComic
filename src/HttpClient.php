<?php

declare(strict_types=1);

namespace PhlyComic;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

use function explode;
use function preg_match;
use function rtrim;
use function strstr;

final class HttpClient implements ClientInterface, RequestFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    /** @param string|UriInterface $uri */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->requestFactory
            ->createRequest($method, $uri)
            ->withHeader(
                'User-Agent',
                'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1'
            );
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $redirects = 0;

        do {
            $response = $this->client->sendRequest($request);
            if ($response->getStatusCode() > 299 && $response->getStatusCode() < 400) {
                $request    = $this->redirectRequest($request->getUri(), $response->getHeaderLine('Location'));
                $redirects += 1;
                continue;
            }

            return $response;
        } while ($redirects < 5);

        return $response;
    }

    private function redirectRequest(UriInterface $baseUri, string $location): RequestInterface
    {
        if (preg_match('#^https?://#', $location)) {
            return $this->createRequest('GET', $location);
        }

        $path  = $location;
        $query = null;
        if (false !== strstr($location, '?')) {
            [$path, $query] = explode('?', $location, 2);
        }

        if (! preg_match('#^/#', $path)) {
            $path = rtrim($baseUri->getPath(), '/') . '/' . $path;
        }

        if (null === $query) {
            return $this->createRequest('GET', $baseUri->withPath($path)->__toString());
        }

        return $this->createRequest('GET', $baseUri->withPath($path)->withQuery($query)->__toString());
    }
}
