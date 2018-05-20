<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitize\Sanitizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MergeController
{
    private $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function __invoke(Request $request, string $entityName): Response
    {
        $sources = $request->request->get('sources');
        if (0 == count($sources)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid sources',
            ]);
        }

        $target = $request->request->get('target');
        if (is_null($target)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid target',
            ]);
        }

        $this->sanitizer->sanitize($entityName, $sources, $target);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
