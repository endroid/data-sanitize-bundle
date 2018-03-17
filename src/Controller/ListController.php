<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

final class ListController
{
    private $kernel;
    private $templating;

    public function __construct(KernelInterface $kernel, Environment $templating)
    {
        $this->kernel = $kernel;
        $this->templating = $templating;
    }

    /**
     * @Route("/{entityName}", defaults={"entityName": null}, requirements={"entityName": "[^/]*"}, name="data_sanitize_list")
     */
    public function __invoke(string $entityName): Response
    {
        // Disable profiler because it conflicts with Vue
        if ($this->kernel->getContainer()->has('profiler')) {
            $this->kernel->getContainer()->get('profiler')->disable();
        }

        return new Response($this->templating->render('@EndroidDataSanitize/list.html.twig', ['entityName' => $entityName]));
    }
}
