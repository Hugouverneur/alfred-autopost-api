<?php

namespace App\Controller;

use App\Entity\AutoRequests;
use App\Repository\AutoRequestsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AutoRequestsController extends AbstractController
{
    #[Route('/auto-requests', name: 'get_all_autorequests', methods: ['GET'])]
    public function getAllUsers(AutoRequestsRepository $autoRequestsRepository): JsonResponse {
        $autoRequests = $autoRequestsRepository->findAll();
        return $this->json($autoRequests, JsonResponse::HTTP_OK);
    }

    #[Route('/auto-requests/{id}', name: 'get_one_autorequest', methods: ['GET'])]
    public function getOneAutoRequest(int $id, AutoRequestsRepository $autoRequestsRepository): JsonResponse {
        $autoRequest = $autoRequestsRepository->find($id);
        if(!$autoRequest) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json($autoRequest, JsonResponse::HTTP_OK);
    }

    #[Route('/auto-requests', name: 'create_autorequest', methods: ['POST'])]
    public function createAutoRequest(EntityManagerInterface $entityManager, Request $req, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse {
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

    #[Route('/auto-requests/{id}', 'edit_autorequest', methods: ['PUT'])]
    public function editAutoRequest(int $id, EntityManagerInterface $entityManager, Request $req, ValidatorInterface $validator, AutoRequestsRepository $autoRequestsRepository): JsonResponse {
        $editAutoRequest = $autoRequestsRepository->find($id);
        if(!$editAutoRequest) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        $reqBody = json_decode($req->getContent(), true);

        isset($reqBody['type']) ? $editAutoRequest->setType($reqBody['type']) : false;
        isset($reqBody['url']) ? $editAutoRequest->setUrl($reqBody['url']) : false;
        isset($reqBody['header']) ? $editAutoRequest->setHeader($reqBody['header']) : false;
        isset($reqBody['body']) ? $editAutoRequest->setBody($reqBody['body']) : false;

        $errors = $validator->validate($editAutoRequest);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(
                ['message' => $errorsString],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($editAutoRequest);
        $entityManager->flush();
        return $this->json($editAutoRequest, JsonResponse::HTTP_OK);
    }

    #[Route('/auto-requests/{id}', 'delete_autorequest', methods: ['DELETE'])]
    public function deleteAutoRequest(int $id, AutoRequestsRepository $autoRequestsRepository, EntityManagerInterface $entityManager): JsonResponse {
        $autoRequests = $autoRequestsRepository->find($id);
        if(!$autoRequests) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        
        $entityManager->remove($autoRequests);
        $entityManager->flush();
        return $this->json(
            ['message' => 'Auto Request successfully deleted'],
            JsonResponse::HTTP_OK
        );
    }
}
