<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle;

class Configuration
{
    /** @var array<string, array<mixed>> */
    private $configuration;

    /** @param array<string, array<mixed>> $configuration */
    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /** @return array<string> */
    public function getNames(): array
    {
        return array_keys($this->configuration['entities']);
    }

    /** @return array<class-string> */
    public function getClasses(): array
    {
        $classes = [];
        foreach ($this->configuration as $name => $configuration) {
            $classes[$name] = $configuration['class'];
        }

        return $classes;
    }

    public function getClass(string $name): string
    {
        return $this->configuration['entities'][$name]['class'];
    }

    /** @return array<string> */
    public function getFields(string $name): array
    {
        $reference = $this->getReference($name);
        $fields = $this->configuration['entities'][$name]['fields'];

        if (!in_array($reference, $fields)) {
            array_unshift($fields, $this->getReference($name));
        }

        return $fields;
    }

    public function getReference(string $name): string
    {
        return $this->configuration['entities'][$name]['reference'];
    }
}
