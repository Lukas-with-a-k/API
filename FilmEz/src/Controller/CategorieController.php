<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categorie;
use Symfony\Component\HttpFoundation\Request;

class CategorieController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    #[Route('/categories', name: 'get_categories', methods: ['GET'])]
public function getCategories(): JsonResponse
{
    $categories = $this->em->getRepository(Categorie::class)->findAll();

    $categoryData = array_map(function ($category) {
        return [
            'id' => $category->getId(),
            'nom' => $category->getNom(),
        ];
    }, $categories);

    return new JsonResponse($categoryData, JsonResponse::HTTP_OK);
}
#[Route('/categories/{id}', name: 'get_category', methods: ['GET'])]
public function getCategory(int $id): JsonResponse
{
    $category = $this->em->getRepository(Categorie::class)->find($id);

    if (!$category) {
        return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $categoryData = [
        'id' => $category->getId(),
        'nom' => $category->getNom(),
    ];

    return new JsonResponse($categoryData, JsonResponse::HTTP_OK);
}
#[Route('/categories', name: 'create_category', methods: ['POST'])]
public function createCategory(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['nom'])) {
        return new JsonResponse(['message' => 'Missing required field: nom'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $category = new Categorie();
    $category->setNom($data['nom']);

    $this->em->persist($category);
    $this->em->flush();

    return new JsonResponse(['message' => 'Category created successfully'], JsonResponse::HTTP_CREATED);
}
#[Route('/categories/{id}', name: 'update_category', methods: ['PUT'])]
public function updateCategory(Request $request, int $id): JsonResponse
{
    $category = $this->em->getRepository(Categorie::class)->find($id);

    if (!$category) {
        return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['nom'])) {
        $category->setNom($data['nom']);
    }

    $this->em->flush();

    return new JsonResponse(['message' => 'Category updated successfully'], JsonResponse::HTTP_OK);
}
#[Route('/categories/{id}', name: 'delete_category', methods: ['DELETE'])]
public function deleteCategory(int $id): JsonResponse
{
    $category = $this->em->getRepository(Categorie::class)->find($id);

    if (!$category) {
        return new JsonResponse(['message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $this->em->remove($category);
    $this->em->flush();

    return new JsonResponse(['message' => 'Category deleted successfully'], JsonResponse::HTTP_OK);
}

}
