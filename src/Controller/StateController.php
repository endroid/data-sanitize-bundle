<?php

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\DataSanitizeBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Endroid\DataSanitize\Sanitizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class StateController
{
    private $entityManager;
    private $sanitizer;

    public function __construct(EntityManagerInterface $entityManager, Sanitizer $sanitizer)
    {
        $this->entityManager = $entityManager;
        $this->sanitizer = $sanitizer;
    }

    /**
     * @Route("/{entityName}/state", defaults={"entityName": null}, requirements={"entityName": "[^/]*"}, name="data_sanitize_state")
     */
    public function stateAction(string $entityName): Response
    {
        $entities = $this->entityManager->getRepository($this->sanitizer->getClass($entityName))->findBy([], ['id' => 'ASC']);
        $fields = $this->sanitizer->getFields($entityName);

        foreach ($entities as &$entity) {
            $data = ['id' => $entity->getId()];
            foreach ($fields as $field) {
                $data[$field] = (string) $entity->{'get'.ucfirst($field)}();
            }
            $entity = $data;
        }

        $state = [
            'entities' => $entities,
            'fields' => $fields,
        ];

        return new JsonResponse($state);
    }
}
