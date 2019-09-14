<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitize\SanitizerFactory;
use Endroid\DataSanitizeBundle\Configuration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class StateController
{
    private $configuration;
    private $sanitizerFactory;

    public function __construct(Configuration $configuration, SanitizerFactory $sanitizerFactory)
    {
        $this->configuration = $configuration;
        $this->sanitizerFactory = $sanitizerFactory;
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
