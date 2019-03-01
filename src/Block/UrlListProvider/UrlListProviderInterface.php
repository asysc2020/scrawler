<?php

namespace Sobak\Scrawler\Block\UrlListProvider;

use GuzzleHttp\Psr7\Response;
use Sobak\Scrawler\Client\Response\Elements\Url;

interface UrlListProviderInterface
{
    /**
     * @return \Sobak\Scrawler\Client\Response\Elements\Url[]
     */
    public function getUrls(): array;

    public function setCurrentUrl(Url $url);

    public function setResponse(Response $response);
}
