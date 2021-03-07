<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ListController
{
    /** @var Environment */
    private $templating;

    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    public function __invoke(string $name): Response
    {
        return new Response($this->templating->render('@EndroidDataSanitize/list.html.twig', ['name' => $name]));
    }
}
