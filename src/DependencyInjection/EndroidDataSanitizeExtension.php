<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class EndroidDataSanitizeExtension extends Extension implements PrependExtensionInterface
{
    /** @param array<mixed> $configs */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $sanitizerDefinition = $container->getDefinition(\Endroid\DataSanitizeBundle\Configuration::class);
        $sanitizerDefinition->setArgument(0, $config);
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Add the package to the assets configuration so the correct manifest is used
        $container->prependExtensionConfig('framework', [
            'assets' => [
                'packages' => [
                    'endroid_data_sanitize' => [
                        'json_manifest_path' => '%kernel.project_dir%/public/bundles/endroiddatasanitize/build/manifest.json',
                    ],
                ],
            ],
        ]);
    }
}
