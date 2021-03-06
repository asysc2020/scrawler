<?php

declare(strict_types=1);

namespace Sobak\Scrawler\Block\ResultWriter;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Exception;
use ReflectionClass;

class DatabaseResultWriter extends AbstractResultWriter
{
    const DRIVER_SQLITE = 'pdo_sqlite';

    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(array $configuration = [])
    {
        if ($this->isDoctrineInstalled() === false) {
            throw new Exception('You need to install Doctrine 2.6 in order to use DatabaseResultWriter');
        }

        if (isset($configuration['connection']['url'])) {
            throw new Exception('Using URL Doctrine configuration is not supported, please pass an array');
        }

        parent::__construct($configuration);
    }

    public function initializeResultWrites(): void
    {
        if ($this->entityName === false) {
            throw new Exception('Entity must be provided to initialize DatabaseResultWriter');
        }

        $entityManager = $this->createEntityManager($this->configuration, $this->entityName);

        $classes = [$entityManager->getClassMetadata($this->entityName)];

        $this->createDatabase($this->configuration);

        $schemaManager = new SchemaTool($entityManager);
        $schemaManager->dropSchema($classes);
        $schemaManager->createSchema($classes);

        $this->entityManager = $entityManager;
    }

    public function write(object $entity): bool
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return true;
    }

    protected function isDoctrineInstalled()
    {
        return class_exists(\Doctrine\ORM\Version::class)
            && \Doctrine\ORM\Version::compare('2.5.0') === -1
            && \Doctrine\ORM\Version::compare('3.0.0') === 1;
    }

    protected function createEntityManager($configuration, string $entityName)
    {
        $reflection = new ReflectionClass($entityName);
        $entityDirectory = dirname($reflection->getFileName());

        return EntityManager::create(
            $configuration['connection'],
            Setup::createAnnotationMetadataConfiguration(
                [$entityDirectory],
                true,
                null,
                null,
                $configuration['simple_annotations'] ?? false
            )
        );
    }

    protected function createDatabase($configuration)
    {
        $databaseNameKey = 'dbname';
        if ($configuration['connection']['driver'] === self::DRIVER_SQLITE) {
            $databaseNameKey = 'path';
        }

        $databaseName = $configuration['connection'][$databaseNameKey];

        $connectionParams = $configuration['connection'];
        unset($connectionParams[$databaseNameKey]);

        $connection = DriverManager::getConnection($connectionParams);

        // Ignore if database already exists
        if (
            $connection->getDriver()->getName() !== self::DRIVER_SQLITE
            && in_array($databaseName, $connection->getSchemaManager()->listDatabases())
        ) {
            $this->logWriter->debug('Database already exists, ignored');
            return;
        }

        $connection->getSchemaManager()->createDatabase($databaseName);

        $this->logWriter->info('Created fresh database: ' . $databaseName);

        $connection->close();
    }
}
