<?php

namespace App\Controller;

use App\Entity\Automations;
use App\Repository\AutomationsRepository;
use App\Repository\UsersRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AutomationsController extends AbstractController
{
    #[Route('/automations', name: 'get_all_automations', methods: ['GET'])]
    public function getAllAutomations(AutomationsRepository $automationsRepository): JsonResponse {
        $automations = $automationsRepository->findAll();
        return $this->json($automations, JsonResponse::HTTP_OK, [], ['groups' => 'automationNestedData']);
    }

    #[Route('/automations/{id}', name: 'get_one_automation', methods: ['GET'])]
    public function getOneAutomations(int $id, AutomationsRepository $automationsRepository): JsonResponse {
        $automation = $automationsRepository->find($id);
        if(!$automation) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json($automation, JsonResponse::HTTP_OK, [], ['groups' => 'automationNestedData']);
    }

    #[Route('/automations', name: 'create_automation', methods: ['POST'])]
    public function createAutomation(EntityManagerInterface $entityManager, Request $req, SerializerInterface $serializer, ValidatorInterface $validator, UsersRepository $usersRepository): JsonResponse {
        $newAutomation = $serializer->deserialize($req->getContent(), Automations::class, 'json');
        $newAutomation->setCreatedAt(new DateTimeImmutable());
        $newAutomation->setUpdatedAt(new DateTime());
        $userId = json_decode($req->getContent(), true)['user_id'];

        if($userId) {
            $user = $usersRepository->find($userId);
            if(!$user) throw new HttpException(JsonResponse::HTTP_NOT_FOUND, 'Users with id '.$userId.' not found');
            $newAutomation->setUserId($user);
        }
        
        $errors = $validator->validate($newAutomation);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(
                ['message' => $errorsString],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        
        $entityManager->persist($newAutomation);
        $entityManager->flush();
        return $this->json($newAutomation, JsonResponse::HTTP_CREATED, [], ['groups' => 'automationData']);
    }

    #[Route('/automations/{id}', 'edit_automation', methods: ['PUT'])]
    public function editAutomations(int $id, EntityManagerInterface $entityManager, Request $req, ValidatorInterface $validator, AutomationsRepository $automationsRepository): JsonResponse {
        $editAutomation = $automationsRepository->find($id);
        if(!$editAutomation) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        $reqBody = json_decode($req->getContent(), true);

        isset($reqBody['name']) ? $editAutomation->setName($reqBody['name']) : false;
        $editAutomation->setUpdatedAt(new DateTime());
        isset($reqBody['alert_user_method']) ? $editAutomation->setAlertUserMethod($reqBody['alert_user_method']) : false;
        isset($reqBody['cron_task']) ? $editAutomation->setCronTask($reqBody['cron_task']) : false;

        $errors = $validator->validate($editAutomation);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(
                ['message' => $errorsString],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($editAutomation);
        $entityManager->flush();
        return $this->json($editAutomation, JsonResponse::HTTP_OK, [], ['groups' => 'automationData']);
    }

    #[Route('/automations/{id}', 'delete_automation', methods: ['DELETE'])]
    public function deleteAutomation(int $id, AutomationsRepository $automationsRepository, EntityManagerInterface $entityManager): JsonResponse {
        $automation = $automationsRepository->find($id);
        if(!$automation) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        
        $entityManager->remove($automation);
        $entityManager->flush();
        return $this->json(
            ['message' => 'Automation successfully deleted'],
            JsonResponse::HTTP_OK
        );
    }
}
