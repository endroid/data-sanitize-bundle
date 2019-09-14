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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MergeController
{
    private $configuration;
    private $sanitizerFactory;

    public function __construct(Configuration $configuration, SanitizerFactory $sanitizerFactory)
    {
        $this->configuration = $configuration;
        $this->sanitizerFactory = $sanitizerFactory;
    }

    public function __invoke(Request $request, string $name): Response
    {
        $sources = $request->query->get('sources');
        if (0 == count($sources)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid sources',
            ]);
        }

        $target = $request->query->get('target');
        if (is_null($target)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid target',
            ]);
        }

        $sanitizer = $this->sanitizerFactory->create($this->configuration->getClass($name));
        $sanitizer->merge($sources, $target);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
