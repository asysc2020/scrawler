<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Sobak\Scrawler\Support\LogWriter;
use Tests\Utils\InMemoryLogWriter;
use Tests\Utils\InMemoryOutputManager;

class LogWriterTest extends TestCase
{
    public function testLoggerDebugVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::DEBUG);

        $this->assertCount(8, InMemoryLogWriter::$log);
        $this->assertEquals('[DEBUG] Debug message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[INFO] Info message', InMemoryLogWriter::$log[1]);
        $this->assertEquals('[NOTICE] Notice message', InMemoryLogWriter::$log[2]);
        $this->assertEquals('[WARNING] Warning message', InMemoryLogWriter::$log[3]);
        $this->assertEquals('[ERROR] Error message', InMemoryLogWriter::$log[4]);
        $this->assertEquals('[CRITICAL] Critical message', InMemoryLogWriter::$log[5]);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[6]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[7]);
    }

    public function testLoggerInfoVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::INFO);

        $this->assertCount(7, InMemoryLogWriter::$log);
        $this->assertEquals('[INFO] Info message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[NOTICE] Notice message', InMemoryLogWriter::$log[1]);
        $this->assertEquals('[WARNING] Warning message', InMemoryLogWriter::$log[2]);
        $this->assertEquals('[ERROR] Error message', InMemoryLogWriter::$log[3]);
        $this->assertEquals('[CRITICAL] Critical message', InMemoryLogWriter::$log[4]);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[5]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[6]);
    }

    public function testLoggerNoticeVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::NOTICE);

        $this->assertCount(6, InMemoryLogWriter::$log);
        $this->assertEquals('[NOTICE] Notice message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[WARNING] Warning message', InMemoryLogWriter::$log[1]);
        $this->assertEquals('[ERROR] Error message', InMemoryLogWriter::$log[2]);
        $this->assertEquals('[CRITICAL] Critical message', InMemoryLogWriter::$log[3]);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[4]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[5]);
    }

    public function testLoggerWarningVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::WARNING);

        $this->assertCount(5, InMemoryLogWriter::$log);
        $this->assertEquals('[WARNING] Warning message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[ERROR] Error message', InMemoryLogWriter::$log[1]);
        $this->assertEquals('[CRITICAL] Critical message', InMemoryLogWriter::$log[2]);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[3]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[4]);
    }

    public function testLoggerErrorVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::ERROR);

        $this->assertCount(4, InMemoryLogWriter::$log);
        $this->assertEquals('[ERROR] Error message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[CRITICAL] Critical message', InMemoryLogWriter::$log[1]);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[2]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[3]);
    }

    public function testLoggerCriticalVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::CRITICAL);

        $this->assertCount(3, InMemoryLogWriter::$log);
        $this->assertEquals('[CRITICAL] Critical message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[1]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[2]);
    }

    public function testLoggerAlertVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::ALERT);

        $this->assertCount(2, InMemoryLogWriter::$log);
        $this->assertEquals('[ALERT] Alert message', InMemoryLogWriter::$log[0]);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[1]);
    }

    public function testLoggerEmergencyVerbosity(): void
    {
        $this->writeLogMessages(LogLevel::EMERGENCY);

        $this->assertCount(1, InMemoryLogWriter::$log);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[0]);
    }

    public function testLogMethod(): void
    {
        $logWriter = new LogWriter([
            [
                'class' => new InMemoryLogWriter(),
                'verbosity' => LogLevel::EMERGENCY,
            ]
        ], new InMemoryOutputManager('test'));

        $logWriter->log(LogLevel::ALERT, 'Alert message');
        $logWriter->log(LogLevel::EMERGENCY, 'Emergency message');

        $this->assertCount(1, InMemoryLogWriter::$log);
        $this->assertEquals('[EMERGENCY] Emergency message', InMemoryLogWriter::$log[0]);
    }

    public function testMessageInterpolation(): void
    {
        $logWriter = new LogWriter([
            [
                'class' => new InMemoryLogWriter(),
                'verbosity' => LogLevel::DEBUG,
            ]
        ], new InMemoryOutputManager('test'));

        $stringableClass = new class {
            public function __toString()
            {
                return 'four';
            }
        };

        $logWriter->debug('Debug message {one} {two} {three} {four}', [
            'one' => 'one',
            'two' => 2,
            'three' => ['ignored'],
            'four' => $stringableClass,
        ]);

        $this->assertCount(1, InMemoryLogWriter::$log);
        $this->assertEquals('[DEBUG] Debug message one 2 {three} four', InMemoryLogWriter::$log[0]);
    }

    protected function writeLogMessages($verbosity)
    {
        $logWriter = new LogWriter([
            [
                'class' => new InMemoryLogWriter(),
                'verbosity' => $verbosity,
            ]
        ], new InMemoryOutputManager('test'));

        $logWriter->debug('Debug message');
        $logWriter->info('Info message');
        $logWriter->notice('Notice message');
        $logWriter->warning('Warning message');
        $logWriter->error('Error message');
        $logWriter->critical('Critical message');
        $logWriter->alert('Alert message');
        $logWriter->emergency('Emergency message');
    }
}
