<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MergeController
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly SanitizerFactory $sanitizerFactory
    ) {
    }

    public function __invoke(Request $request, string $name): Response
    {
        /** @var array<string> $sourceIds */
        $sourceIds = $request->query->all('sources');
        if (0 === count($sourceIds)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid sources',
            ]);
        }

        $targetId = (string) $request->query->get('target');

        $sanitizer = $this->sanitizerFactory->create($this->configuration->getClass($name));
        $sanitizer->merge($sourceIds, $targetId);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
