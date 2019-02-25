<?php

namespace Sobak\Scrawler\Matcher;

use Sobak\Scrawler\Support\Utils;

class XpathTextMatcher extends AbstractMatcher
{
    public function match(): ?string
    {
        $result = $this->getCrawler()->filterXPath($this->getMatchBy());

        return $result->count() === 0 ? null : Utils::trimWhitespace($result->text());
    }
}