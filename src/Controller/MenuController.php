<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitize\Sanitizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class MenuController
{
    private $templating;
    private $router;
    private $sanitizer;

    public function __construct(Environment $templating, RouterInterface $router, Sanitizer $sanitizer)
    {
        $this->templating = $templating;
        $this->router = $router;
        $this->sanitizer = $sanitizer;
    }

    /**
     * @Route("/menu")
     */
    public function __invoke()
    {
        $menu = [];
        $config = $this->sanitizer->getConfiguration();
        foreach ($config as $entityName => $entityConfig) {
            $menu[] = [
                'label' => ucfirst(str_replace('_', ' ', $entityName)),
                'url' => $this->router->generateUrl('data_sanitize_list', ['entityName' => $entityName]),
            ];
        }

        return new Response($this->templating->render('@EndroidDataSanitize/menu.html.twig', ['menu' => $menu]));
    }
}
