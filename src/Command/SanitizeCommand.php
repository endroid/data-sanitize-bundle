<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Command;

use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Configuration;
use Endroid\SimpleSpreadsheet\Adapter\ArrayAdapter;
use Endroid\SimpleSpreadsheet\SimpleSpreadsheet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'endroid:data-sanitize:sanitize', description: 'Sanitize data')]
final class SanitizeCommand extends Command
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly SanitizerFactory $sanitizerFactory,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED)->addArgument('source', InputArgument::REQUIRED);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $this->requireStringArgument($input->getArgument('name'));
        $filename = $this->requireStringArgument($input->getArgument('source'));

        $class = $this->configuration->getClass($name);
        $sanitizer = $this->sanitizerFactory->create($class);

        $spreadsheet = new SimpleSpreadsheet();
        $spreadsheet->load($filename);

        /** @var array<array<mixed>> $data ArrayAdapter returns the loaded sheets without transforming their rows. */
        $data = $spreadsheet->save(ArrayAdapter::class);

        $merges = [];
        foreach ($data as $sheet) {
            foreach (array_keys($sheet) as $key) {
                if (!is_array($sheet[$key] ?? null)) {
                    throw new \InvalidArgumentException('Every spreadsheet row must contain id and target_id columns.');
                }

                $row = $sheet[$key];
                if (
                    !is_string($row['id'] ?? null) && !is_int($row['id'] ?? null)
                    || !is_string($row['target_id'] ?? null) && !is_int($row['target_id'] ?? null)
                ) {
                    throw new \InvalidArgumentException(
                        'The id and target_id columns must contain strings or integers.',
                    );
                }

                $id = (string) $row['id'];
                $targetId = (string) $row['target_id'];
                if ($targetId === $id) {
                    continue;
                }

                $merges[$targetId][] = $id;
            }
        }

        foreach ($merges as $target => $sources) {
            $sanitizer->merge($sources, strval($target));
        }

        return Command::SUCCESS;
    }

    private function requireStringArgument(mixed $value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Invalid name or source parameter passed');
        }

        return $value;
    }
}
