<?php

declare(strict_types=1);

namespace Sobak\Scrawler\Block\ResultWriter;

use Sobak\Scrawler\Output\Outputter;
use Sobak\Scrawler\Output\OutputWriterInterface;

abstract class FileResultWriter extends AbstractResultWriter implements OutputWriterInterface
{
    /** @var Outputter */
    protected $outputter;

    public function getOutputter(): Outputter
    {
        return $this->outputter;
    }

    public function setOutputter(Outputter $outputter): void
    {
        $this->outputter = $outputter;
    }

    protected function writeToFile(string $contents, string $extension): bool
    {
        $directory = '';
        if (isset($this->configuration['directory'])) {
            $directory = trim($this->configuration['directory'] ?? '', '/') . '/';

            $this->outputter->createDirectory($directory, true);
        }

        $filename = $directory . uniqid() . '.' . $extension;
        $this->outputter->writeToFile($filename, $contents);

        return true;
    }
}
