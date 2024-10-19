<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle;

final readonly class Configuration
{
    public function __construct(
        /** @var array<string, array<mixed>> */
        private array $configuration,
    ) {
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
