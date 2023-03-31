<?php

namespace App\Controller;

use App\Entity\AutoRequests;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AutoRequestsController extends AbstractController
{
    #[Route('/auto-requests', name: 'create_autorequest', methods: ['POST'])]
    public function createAutoRequest(
                                EntityManagerInterface $entityManager,
                                Request $req,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator
                                ): JsonResponse
    {
        $newAutoRequest = $serializer->deserialize($req->getContent(), AutoRequests::class, 'json');
        
        $errors = $validator->validate($newAutoRequest);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(
                ['message' => $errorsString],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        
        $entityManager->persist($newAutoRequest);
        $entityManager->flush();
        return $this->json($newAutoRequest, JsonResponse::HTTP_CREATED);
    }
}
