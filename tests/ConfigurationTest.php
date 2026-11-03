<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Tests;

use Endroid\DataSanitizeBundle\Configuration;
use Endroid\DataSanitizeBundle\DependencyInjection\Configuration as ConfigurationDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

#[CoversClass(Configuration::class)]
#[CoversClass(ConfigurationDefinition::class)]
final class ConfigurationTest extends TestCase
{
    public function testProcessedEntitiesKeepNamesClassesAndReferenceFields(): void
    {
        $configuration = $this->createConfiguration([
            'entities' => [
                'tasks' => ['class' => \stdClass::class, 'fields' => ['title']],
                '0' => ['class' => \ArrayObject::class, 'fields' => ['uuid', 'label'], 'reference' => 'uuid'],
            ],
        ]);

        self::assertSame(['tasks', '0'], $configuration->getNames());
        self::assertSame(['tasks' => \stdClass::class, 0 => \ArrayObject::class], $configuration->getClasses());
        self::assertSame(\ArrayObject::class, $configuration->getClass('0'));
        self::assertSame('id', $configuration->getReference('tasks'));
        self::assertSame(['id', 'title'], $configuration->getFields('tasks'));
        self::assertSame(['uuid', 'label'], $configuration->getFields('0'));
    }

    public function testEmptyConfigurationHasNoEntities(): void
    {
        $configuration = $this->createConfiguration([]);

        self::assertSame([], $configuration->getNames());
        self::assertSame([], $configuration->getClasses());
    }

    public function testUnknownEntityFailsExplicitly(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('Unknown entity "missing".');

        $this->createConfiguration([])->getClass('missing');
    }

    public function testUnknownConfiguredClassIsRejected(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->createConfiguration(['entities' => ['tasks' => ['class' => 'MissingEntityClass']]]);
    }

    private function createConfiguration(array $config): Configuration
    {
        $normalized = new Processor()->processConfiguration(new ConfigurationDefinition(), [$config]);

        return new Configuration($normalized);
    }
}
