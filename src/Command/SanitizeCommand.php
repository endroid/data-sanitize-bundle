<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Command;

use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Configuration;
use Endroid\SimpleExcel\SimpleExcel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SanitizeCommand extends Command
{
    protected static $defaultName = 'endroid:data-sanitize:sanitize';

    private $configuration;
    private $sanitizerFactory;

    public function __construct(
        $name = null,
        Configuration $configuration,
        SanitizerFactory $sanitizerFactory
    ) {
        parent::__construct($name);

        $this->configuration = $configuration;
        $this->sanitizerFactory = $sanitizerFactory;
    }

    protected function configure()
    {
        $this
            ->setDescription('Sanitize data')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('source', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $filename = $input->getArgument('source');

        $class = $this->configuration->getClass(strval($name));
        $sanitizer = $this->sanitizerFactory->create($class);

        $excel = new SimpleExcel();
        $excel->loadFromFile(strval($filename));

        $data = $excel->saveToArray();

        $merges = [];
        foreach ($data as $sheet) {
            foreach ($sheet as $row) {
                if ($row['target_id'] !== $row['id']) {
                    $merges[$row['target_id']][] = $row['id'];
                }
            }
        }

        foreach ($merges as $target => $sources) {
            if ('' === $target) {
                $sanitizer->delete($sources);
            } else {
                $sanitizer->merge($sources, strval($target));
            }
        }

        return 0;
    }
}
