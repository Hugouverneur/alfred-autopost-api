<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'get_all_users', methods: ['GET'])]
    public function getAllUsers(UsersRepository $usersRepository): JsonResponse {
        $users = $usersRepository->findAll();
        return $this->json($users, JsonResponse::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'get_one_user', methods: ['GET'])]
    public function getOneUsers(int $id, UsersRepository $usersRepository): JsonResponse {
        $user = $usersRepository->find($id);
        if(!$user) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json($user, JsonResponse::HTTP_OK);
    }

    #[Route('/user/email', name: 'get_user_by_email', methods: ['POST'])]
    public function getUserByEmail(UsersRepository $usersRepository, Request $req, SerializerInterface $serializer): JsonResponse {
        $userEmail = $req->toArray()['email'];
        $user = $usersRepository->findOneByEmail($userEmail);
        if(!$user) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        return $this->json($user, JsonResponse::HTTP_OK);
    }

    #[Route('/users/signup', name: 'create_users', methods: ['POST'])]
    public function createUsers(EntityManagerInterface $entityManager, Request $req, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): JsonResponse {
        $newUser = $serializer->deserialize($req->getContent(), Users::class, 'json');
        
        $errors = $validator->validate($newUser);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(
                ['message' => $errorsString],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        
        $newUser->setPassword($passwordHasher->hashPassword($newUser, $newUser->getPassword()));
        $entityManager->persist($newUser);
        $entityManager->flush();
        return $this->json($newUser, JsonResponse::HTTP_CREATED);
    }

    #[Route('/users/{id}', 'app_edit_user', methods: ['PUT'])]
    public function editUser(int $id, EntityManagerInterface $entityManager, Request $req, ValidatorInterface $validator, UsersRepository $usersRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse {
        $editUser = $usersRepository->find($id);
        if(!$editUser) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        $reqBody = json_decode($req->getContent(), true);

        isset($reqBody['email']) ? $editUser->setEmail($reqBody['email']) : false;
        isset($reqBody['password']) ? $editUser->setPassword($passwordHasher->hashPassword($editUser, $reqBody['password'])) : false;
        $errors = $validator->validate($editUser);
        if(count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(
                ['message' => $errorsString],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($editUser);
        $entityManager->flush();
        return $this->json($editUser, JsonResponse::HTTP_OK);
    }

    #[Route('/users/{id}', 'app_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, UsersRepository $usersRepository, EntityManagerInterface $entityManager): JsonResponse {
        $user = $usersRepository->find($id);
        if(!$user) {
            return $this->json(null, JsonResponse::HTTP_NOT_FOUND);
        }
        
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->json(
            ['message' => 'User successfully deleted'],
            JsonResponse::HTTP_OK
        );
    }
}
