<?php

declare(strict_types=1);

namespace App\Tests;

use Endroid\DataSanitizeBundle\Configuration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BundleTest extends KernelTestCase
{
    public function testBundleBootsAndRegistersTheSanitizeCommand(): void
    {
        $kernel = self::bootKernel();

        self::assertSame([], self::getContainer()->get(Configuration::class)->getNames());
        $command = new Application($kernel)->find('endroid:data-sanitize:sanitize');
        self::assertTrue($command->getDefinition()->getArgument('name')->isRequired());
        self::assertTrue($command->getDefinition()->getArgument('source')->isRequired());
    }
}
