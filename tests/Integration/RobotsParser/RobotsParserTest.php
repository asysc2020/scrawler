<?php

namespace Tests\Integration\RobotsParser;

use Sobak\Scrawler\Block\Matcher\CssSelectorHtmlMatcher;
use Sobak\Scrawler\Block\Matcher\CssSelectorListMatcher;
use Sobak\Scrawler\Block\RobotsParser\DefaultRobotsParser;
use Sobak\Scrawler\Configuration\ObjectConfiguration;
use Sobak\Scrawler\Scrawler;
use Tests\Integration\IntegrationTest;
use Tests\Utils\BasicConfigurationProvider;
use Tests\Utils\InMemoryResultWriter;
use Tests\Utils\SimpleMatchEntity;

/**
 * @covers \Sobak\Scrawler\Block\RobotsParser\AbstractRobotsParser
 * @covers \Sobak\Scrawler\Block\RobotsParser\DefaultRobotsParser
 * @covers \Sobak\Scrawler\Block\UrlListProvider\EmptyUrlListProvider
 * @covers \Sobak\Scrawler\Scrawler
 */
class RobotsParserTest extends IntegrationTest
{
    public function testRobotsTxtParser(): void
    {
        $config = BasicConfigurationProvider::getConfiguration()
            ->setBaseUrl(IntegrationTest::getHostUrl())
            ->setRobotsParser(new DefaultRobotsParser())
            ->addObjectDefinition('test', new CssSelectorListMatcher('body'), function (ObjectConfiguration $object) {
                $object
                    ->addFieldDefinition('match', new CssSelectorHtmlMatcher('span.match'))
                    ->addEntityMapping(SimpleMatchEntity::class)
                    ->addResultWriter(SimpleMatchEntity::class, new InMemoryResultWriter())
                ;
            })
        ;

        $scrawler = new Scrawler($config, __DIR__ . '/output');
        $scrawler->run();

        $this->assertEmpty(InMemoryResultWriter::$results);
    }
}
