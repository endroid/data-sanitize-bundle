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

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('source', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $filename = $input->getArgument('source');

        if (!is_string($name) || !is_string($filename)) {
            throw new \Exception('Invalid name or source parameter passed');
        }

        $class = $this->configuration->getClass($name);
        $sanitizer = $this->sanitizerFactory->create($class);

        $spreadsheet = new SimpleSpreadsheet();
        $spreadsheet->load($filename);

        $data = $spreadsheet->save(ArrayAdapter::class);

        $merges = [];
        foreach ($data as $sheet) {
            foreach ($sheet as $row) {
                if ($row['target_id'] !== $row['id']) {
                    $merges[$row['target_id']][] = $row['id'];
                }
            }
        }

        foreach ($merges as $target => $sources) {
            $sanitizer->merge($sources, strval($target));
        }

        return Command::SUCCESS;
    }
}
