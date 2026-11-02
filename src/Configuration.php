<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle;

final readonly class Configuration
{
    public function __construct(
        /** @var array{entities?: array<array-key, array{class: class-string, fields: array<array-key, string>, reference: string}>} */
        private array $configuration,
    ) {}

    /** @return array<string> */
    public function getNames(): array
    {
        return array_map(strval(...), array_keys($this->configuration['entities'] ?? []));
    }

    /** @return array<class-string> */
    public function getClasses(): array
    {
        $classes = [];
        foreach ($this->configuration['entities'] ?? [] as $name => $configuration) {
            $classes[$name] = $configuration['class'];
        }

        return $classes;
    }

    /** @return class-string */
    public function getClass(string $name): string
    {
        return $this->getEntity($name)['class'];
    }

    /** @return array<string> */
    public function getFields(string $name): array
    {
        $reference = $this->getReference($name);
        $fields = $this->getEntity($name)['fields'];

        if (!in_array($reference, $fields, strict: true)) {
            array_unshift($fields, $this->getReference($name));
        }

        return $fields;
    }

    public function getReference(string $name): string
    {
        return $this->getEntity($name)['reference'];
    }

    /** @return array{class: class-string, fields: array<array-key, string>, reference: string} */
    private function getEntity(string $name): array
    {
        return (
            $this->configuration['entities'][$name] ?? throw new \OutOfBoundsException(
                'Unknown entity "' . $name . '".',
            )
        );
    }
}
