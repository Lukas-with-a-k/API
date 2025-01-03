<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Commentaire;
use App\Entity\Like;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/likes', name: 'add_like', methods: ['POST'])]
public function addLike(Request $request, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['film_id']) && !isset($data['commentaire_id'])) {
        return new JsonResponse(['message' => 'Missing film_id or commentaire_id'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $user = $this->getUser();
    if (!$user) {
        return new JsonResponse(['message' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    $like = new Like();
    $like->setUtilisateur($user);

    if (isset($data['film_id'])) {
        $film = $em->getRepository(Film::class)->find($data['film_id']);
        if (!$film) {
            return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $like->setFilm($film);
    }

    if (isset($data['commentaire_id'])) {
        $commentaire = $em->getRepository(Commentaire::class)->find($data['commentaire_id']);
        if (!$commentaire) {
            return new JsonResponse(['message' => 'Comment not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $like->setCommentaire($commentaire);
    }

    $em->persist($like);
    $em->flush();

    return new JsonResponse(['message' => 'Like added successfully'], JsonResponse::HTTP_CREATED);
}
#[Route('/likes/{id}', name: 'remove_like', methods: ['DELETE'])]
public function removeLike(int $id, EntityManagerInterface $em): JsonResponse
{
    $like = $em->getRepository(Like::class)->find($id);

    if (!$like) {
        return new JsonResponse(['message' => 'Like not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $user = $this->getUser();
    if ($like->getUtilisateur() !== $user) {
        return new JsonResponse(['message' => 'Unauthorized'], JsonResponse::HTTP_FORBIDDEN);
    }

    $em->remove($like);
    $em->flush();

    return new JsonResponse(['message' => 'Like removed successfully'], JsonResponse::HTTP_OK);
}
#[Route('/likes/count/{id}', name: 'count_likes', methods: ['GET'])]
public function countLikes(int $id, EntityManagerInterface $em): JsonResponse
{
    $film = $em->getRepository(Film::class)->find($id);

    if (!$film) {
        return new JsonResponse(['message' => 'Film not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $likeCount = $film->getLikes()->count();

    return new JsonResponse(['likes' => $likeCount], JsonResponse::HTTP_OK);
}

}