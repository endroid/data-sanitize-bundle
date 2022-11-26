<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Controller;

use Endroid\DataSanitizeBundle\Configuration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class MenuController
{
    public function __construct(
        private Environment $templating,
        private RouterInterface $router,
        private Configuration $configuration
    ) {
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
