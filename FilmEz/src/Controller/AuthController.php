<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\AuthController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
#[Route('/auth')]

class AuthController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $Hasher, UserRepository $userRepo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'message' => 'Missing email or password',
            ], 400);
        }

        $userExist = $userRepo->findOneBy(['email' => $data['email']]);
        if ($userExist) {
            return $this->json([
                'message' => 'User already exists',
            ], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($Hasher->hashPassword($user, $data['password']));
        $roles = $data['roles'] ?? ['ROLE_USER'];
        $user->setRoles($roles);
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json([
            'message' => 'User registered successfully',
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $Hasher, UserRepository $userRepo, JWTTokenManagerInterface $jwt): JsonResponse
    {        
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'message' => 'Missing email or password',
            ], 400);
        }

        $user = $userRepo->findOneBy(['email' => $data['email']]);
        if (!$user || !$Hasher->isPasswordValid($user, $data['password'])) {
            return $this->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $token = $jwt->create($user);


        return $this->json([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }
}
