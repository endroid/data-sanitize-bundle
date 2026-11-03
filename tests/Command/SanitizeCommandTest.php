<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Tests\Command;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration as OrmConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Endroid\DataSanitize\RelationFinder;
use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Command\SanitizeCommand;
use Endroid\DataSanitizeBundle\Configuration;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(SanitizeCommand::class)]
#[UsesClass(Configuration::class)]
final class SanitizeCommandTest extends TestCase
{
    public function testNumericSpreadsheetIdsMergeWithoutDeletingTheTarget(): void
    {
        $ormConfiguration = new OrmConfiguration();
        $ormConfiguration->setMetadataDriverImpl(new AttributeDriver([__DIR__]));
        if (method_exists($ormConfiguration, 'enableNativeLazyObjects')) {
            $ormConfiguration->enableNativeLazyObjects(true);
        } else {
            $ormConfiguration->setProxyDir(sys_get_temp_dir());
            $ormConfiguration->setProxyNamespace(__NAMESPACE__ . '\\Proxies');
        }
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $entityManager = new EntityManager($connection, $ormConfiguration);
        new SchemaTool($entityManager)->createSchema([$entityManager->getClassMetadata(MergeItem::class)]);
        foreach ([1, 2, 3] as $id) {
            $connection->insert('merge_items', ['id' => $id]);
        }

        $filename = tempnam(sys_get_temp_dir(), 'sanitize-');
        self::assertNotFalse($filename);
        try {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getActiveSheet()->fromArray([['id', 'target_id'], [1, 2], [2, 2], [3, 2]]);
            new Xlsx($spreadsheet)->save($filename);
            $configuration = new Configuration([
                'entities' => ['items' => ['class' => MergeItem::class, 'fields' => [], 'reference' => 'id']],
            ]);
            $factory = new SanitizerFactory($entityManager, new RelationFinder($entityManager));
            $tester = new CommandTester(new SanitizeCommand($configuration, $factory));

            self::assertSame(0, $tester->execute(['name' => 'items', 'source' => $filename]));
            self::assertSame([2], $connection->fetchFirstColumn('SELECT id FROM merge_items'));
        } finally {
            unlink($filename);
            $entityManager->close();
        }
    }
}

#[ORM\Entity]
#[ORM\Table(name: 'merge_items')]
class MergeItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public int $id;
}
