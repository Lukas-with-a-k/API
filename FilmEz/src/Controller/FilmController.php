<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Categorie;
use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FilmController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/films', name: 'get_films', methods: ['GET'])]
    public function getFilms(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;

        $films = $this->em->getRepository(Film::class)->findBy([], null, $limit, ($page - 1) * $limit);

        $filmData = array_map(function (Film $film) {
            return [
                'id' => $film->getId(),
                'titre' => $film->getTitre(),
                'description' => $film->getDescription(),
                'dateSortie' => $film->getDateSortie()->format('Y-m-d'),
                'categories' => $film->getCategories()->map(fn($category) => [
                    'id' => $category->getId(),
                    'nom' => $category->getNom(),
                ])->toArray(),
            ];
        }, $films);

        return new JsonResponse($filmData, JsonResponse::HTTP_OK);
    }

    #[Route('/films/{id}', name: 'get_film', methods: ['GET'])]
    public function getFilm(int $id): JsonResponse
    {
        $film = $this->em->getRepository(Film::class)->find($id);

        if (!$film) {
            return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $filmData = [
            'id' => $film->getId(),
            'titre' => $film->getTitre(),
            'description' => $film->getDescription(),
            'dateSortie' => $film->getDateSortie()->format('Y-m-d'),
            'categories' => $film->getCategories()->map(fn($category) => [
                'id' => $category->getId(),
                'nom' => $category->getNom(),
            ])->toArray(),
        ];

        return new JsonResponse($filmData, JsonResponse::HTTP_OK);
    }

    #[Route('/films', name: 'create_film', methods: ['POST'])]
    public function createFilm(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['titre'], $data['dateSortie'], $data['categories'])) {
            return new JsonResponse(['message' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $film = new Film();
            $film->setTitre($data['titre']);
            $film->setDescription($data['description'] ?? null);
            $film->setDateSortie(new \DateTime($data['dateSortie']));

            foreach ($data['categories'] as $categoryId) {
                $category = $this->em->getRepository(Categorie::class)->find($categoryId);
                if ($category) {
                    $film->addCategorie($category);
                }
            }

            $this->em->persist($film);
            $this->em->flush();

            return new JsonResponse(['message' => 'Film created successfully'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/films/{id}', name: 'update_film', methods: ['PUT'])]
    public function updateFilm(Request $request, int $id): JsonResponse
    {
        $film = $this->em->getRepository(Film::class)->find($id);

        if (!$film) {
            return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) {
            $film->setTitre($data['titre']);
        }

        if (isset($data['description'])) {
            $film->setDescription($data['description']);
        }

        if (isset($data['dateSortie'])) {
            try {
                $film->setDateSortie(new \DateTime($data['dateSortie']));
            } catch (\Exception $e) {
                return new JsonResponse(['message' => 'Invalid date format'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        if (isset($data['categories'])) {
            $film->getCategories()->clear();
            foreach ($data['categories'] as $categoryId) {
                $category = $this->em->getRepository(Categorie::class)->find($categoryId);
                if ($category) {
                    $film->addCategorie($category);
                }
            }
        }

        $this->em->flush();

        return new JsonResponse(['message' => 'Film updated successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/films/{id}', name: 'delete_film', methods: ['DELETE'])]
    public function deleteFilm(int $id): JsonResponse
    {
        $film = $this->em->getRepository(Film::class)->find($id);

        if (!$film) {
            return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->em->remove($film);
        $this->em->flush();

        return new JsonResponse(['message' => 'Film deleted successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/films/{id}/comments', name: 'add_comment', methods: ['POST'])]
    public function addComment(Request $request, int $id): JsonResponse
    {
        $film = $this->em->getRepository(Film::class)->find($id);

        if (!$film) {
            return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['contenu'])) {
            return new JsonResponse(['message' => 'Missing required field: contenu'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu($data['contenu']);
        $commentaire->setDateCreation(new \DateTime());
        $commentaire->setFilm($film);

        $this->em->persist($commentaire);
        $this->em->flush();

        return new JsonResponse(['message' => 'Comment added successfully'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/films/{id}/comments', name: 'get_comments', methods: ['GET'])]
    public function getComments(int $id): JsonResponse
    {
        $film = $this->em->getRepository(Film::class)->find($id);

        if (!$film) {
            return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $comments = $film->getCommentaires();

        $commentData = array_map(function (Commentaire $comment) {
            return [
                'id' => $comment->getId(),
                'contenu' => $comment->getContenu(),
                'dateCreation' => $comment->getDateCreation()->format('Y-m-d H:i:s'),
            ];
        }, $comments->toArray());

        return new JsonResponse($commentData, JsonResponse::HTTP_OK);
    }
}
