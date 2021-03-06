<?php

declare(strict_types=1);

namespace Sobak\Scrawler\Block\ResultWriter;

use Exception;
use Sobak\Scrawler\Block\ResultWriter\FilenameProvider\FilenameProviderInterface;
use Sobak\Scrawler\Output\OutputManagerInterface;
use Sobak\Scrawler\Output\OutputWriterInterface;

abstract class FileResultWriter extends AbstractResultWriter implements
    FileResultWriterInterface,
    OutputWriterInterface
{
    protected $directory;

    protected $filename;

    /** @var OutputManagerInterface */
    protected $outputManager;

    public function __construct(array $configuration = [])
    {
        if (
            isset($configuration['filename']) === false
            || ($configuration['filename'] instanceof FilenameProviderInterface) === false
        ) {
            throw new Exception("For the FileResultWriter you must set the FilenameProvider under 'filename' key");
        }

        parent::__construct($configuration);
    }

    public function getFilenameProvider(): FilenameProviderInterface
    {
        return $this->configuration['filename'];
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getOutputManager(): OutputManagerInterface
    {
        return $this->outputManager;
    }

    public function setOutputManager(OutputManagerInterface $outputManager): void
    {
        $this->outputManager = $outputManager;
    }

    public function initializeResultWrites(): void
    {
        $directory = '';
        if (isset($this->configuration['directory'])) {
            $directory = trim($this->configuration['directory'] ?? '', '/') . '/';

            $this->logWriter->info("Created {$directory} directory to store result files");

            $this->outputManager->createDirectory($directory, true);
        }

        $this->directory = $directory;
    }

    protected function writeToFile(string $contents, ?string $extension): bool
    {
        $filename = $this->directory . $this->filename . '.' . $extension;
        if ($extension === null) {
            $filename = $this->directory . $this->filename;
        }

        $this->outputManager->writeToFile($filename, $contents);

        return true;
    }
}
