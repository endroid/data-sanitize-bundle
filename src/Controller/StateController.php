<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class StateController
{
    public function __construct(
        private Configuration $configuration,
        private SanitizerFactory $sanitizerFactory,
    ) {
    }

    public function __invoke(string $name): Response
    {
        $sanitizer = $this->sanitizerFactory->create($this->configuration->getClass($name));

        return new JsonResponse([
            'fields' => array_map([$sanitizer, 'getAlias'], $this->configuration->getFields($name)),
            'entities' => $sanitizer->getData($this->configuration->getFields($name)),
        ]);
    }
}
