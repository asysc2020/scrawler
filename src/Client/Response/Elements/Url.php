<?php

declare(strict_types=1);

namespace Sobak\Scrawler\Client\Response\Elements;

class Url
{
    protected $currentUrl;

    protected $method;

    protected $rawUrl;

    protected $url;

    public function __construct(string $url, ?string $currentUrl = null, string $method = 'GET')
    {
        $this->url = $this->normalizeUrl($url, $currentUrl);
        $this->currentUrl = $currentUrl;
        $this->method = strtoupper($method);
        $this->rawUrl = $url;
    }

    public function getCurrentUrl(): ?string
    {
        return $this->currentUrl;
    }

    public function getDomain(?string $url = null): string
    {
        return $this->extractDomain($url ?? $this->url);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRawUrl(): string
    {
        return $this->rawUrl;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->url;
    }

    protected function extractDomain(string $url): string
    {
        $components = parse_url($url);
        $domain = '';

        if (isset($components['scheme']) === false) {
            throw new \Exception('Cannot get domain from protocol-relative URL');
        }

        $domain .= $components['scheme'] . '://';

        if (isset($components['user'])) {
            $domain .= "{$components['user']}:";
        }
        if (isset($components['pass'])) {
            $domain .= "{$components['pass']}@";
        }

        $domain .= $components['host'];

        if (isset($components['port'])) {
            $domain .= ":{$components['port']}";
        }

        return $domain;
    }

    protected function normalizeUrl(string $url, ?string $currentUrl)
    {
        if ($currentUrl === null) {
            $this->checkCurrentUrl($url);
        }

        // Resolve URL with relative protocol
        if (strpos($url, '//') === 0) {
            $currentScheme = parse_url($currentUrl, PHP_URL_SCHEME);
            $url = $currentScheme . ':' . $url;
        }

        $url = $this->removeAnchor(trim($url));

        if ($url === '') {
            return $currentUrl;
        }

        if ($url[0] === '?') {
            return rtrim($this->removeAnchor($this->removeQueryString($currentUrl)), '/') . $url;
        }

        // If the URL is absolute, we're done
        if (parse_url($url, PHP_URL_SCHEME) !== null) {
            return $url;
        }

        return $this->resolveRelativePath($url, $currentUrl);
    }

    protected function resolveRelativePath(string $url, string $currentUrl): string
    {
        if ('/' === $url[0]) {
            return $this->getDomain($currentUrl) . $url;
        }

        $currentUrlSegments = explode('/', rtrim($currentUrl, '/'));

        // If there is no path attatched to the current URL
        // we only need to append new path to it and return
        if (count($currentUrlSegments) === 3) {
            return implode('/', $currentUrlSegments) . '/' . $url;
        }

        foreach (explode('/', $url) as $pathSegment) {
            // Three because we need to account for slashes in the protocol
            if (count($currentUrlSegments) <= 3) {
                break;
            }

            array_pop($currentUrlSegments);

            if ($pathSegment !== '..') {
                $currentUrlSegments[] = $pathSegment;
            }
        }

        return implode('/', $currentUrlSegments);
    }

    protected function checkCurrentUrl($url): void
    {
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            throw new \Exception('First URL must be absolute');
        }

        if (in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https']) === false) {
            throw new \Exception('Only http and https protocols are supported');
        }
    }

    protected function removeAnchor(string $url): string
    {
        if (($position = strpos($url, '#')) !== false) {
            return substr($url, 0, $position);
        }

        return $url;
    }

    protected function removeQueryString(string $url): string
    {
        if (($position = strpos($url, '?')) !== false) {
            return substr($url, 0, $position);
        }

        return $url;
    }
}
