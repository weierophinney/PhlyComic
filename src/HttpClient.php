<?php

declare(strict_types=1);

namespace PhlyComic;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class HttpClient implements ClientInterface, RequestFactoryInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
    ) {
    }

    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->requestFactory->createRequest($method, $uri);
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
