<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitizeBundle\Configuration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class MenuController
{
    /** @var Environment */
    private $templating;

    /** @var RouterInterface */
    private $router;

    /** @var Configuration */
    private $configuration;

    public function __construct(Environment $templating, RouterInterface $router, Configuration $configuration)
    {
        $this->templating = $templating;
        $this->router = $router;
        $this->configuration = $configuration;
    }

    public function __invoke(): Response
    {
        $menu = [];
        foreach ($this->configuration->getNames() as $name) {
            $menu[] = [
                'label' => ucfirst(str_replace('_', ' ', $name)),
                'url' => $this->router->generate('data_sanitize_list', ['name' => $name]),
            ];
        }

        return new Response($this->templating->render('@EndroidDataSanitize/menu.html.twig', ['menu' => $menu]));
    }
}
