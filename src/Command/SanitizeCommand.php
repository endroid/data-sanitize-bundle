<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Command;

use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Configuration;
use Endroid\SimpleSpreadsheet\Adapter\ArrayAdapter;
use Endroid\SimpleSpreadsheet\SimpleSpreadsheet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SanitizeCommand extends Command
{
    protected static $defaultName = 'endroid:data-sanitize:sanitize';

    /** @var Configuration */
    private $configuration;

    /** @var SanitizerFactory */
    private $sanitizerFactory;

    public function __construct(
        Configuration $configuration,
        SanitizerFactory $sanitizerFactory
    ) {
        parent::__construct();

        $this->configuration = $configuration;
        $this->sanitizerFactory = $sanitizerFactory;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Sanitize data')
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

        return 0;
    }
}
