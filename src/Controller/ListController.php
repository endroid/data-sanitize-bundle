<?php

declare(strict_types=1);

namespace Endroid\DataSanitizeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ListController
{
    public function __construct(
        private Environment $templating
    ) {
    }

    public function __invoke(string $name): Response
    {
        return new Response($this->templating->render('@EndroidDataSanitize/list.html.twig', ['name' => $name]));
    }
}
