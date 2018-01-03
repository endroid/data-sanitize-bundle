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
use Twig\Environment;

final class ListController
{
    private $templating;

    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @Route("/{entityName}", defaults={"entityName": null}, requirements={"entityName": "[^/]*"}, name="endroid_data_sanitize_list")
     */
    public function __invoke(string $entityName): Response
    {
        return new Response($this->templating->render('@EndroidDataSanitize/list.html.twig', ['entityName' => $entityName]));
    }
}
