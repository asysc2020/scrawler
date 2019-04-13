<?php

declare(strict_types=1);

namespace Sobak\Scrawler\Configuration;

use Psr\Log\LogLevel;
use Sobak\Scrawler\Block\LogWriter\ConsoleLogWriter;
use Sobak\Scrawler\Block\LogWriter\TextfileLogWriter;

class DefaultConfigurationProvider
{
    public function setDefaultConfiguration(Configuration $configuration): Configuration
    {
        $configuration
            ->addLogWriter(new ConsoleLogWriter(), LogLevel::NOTICE)
            ->addLogWriter(new TextfileLogWriter())
        ;

        return $configuration;
    }
}
