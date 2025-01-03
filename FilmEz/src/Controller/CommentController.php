<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/comments/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment(int $id): JsonResponse
    {
        $comment = $this->em->getRepository(Commentaire::class)->find($id);

        if (!$comment) {
            return new JsonResponse(['message' => 'Comment not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        if ($comment->getUtilisateur() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Unauthorized'], JsonResponse::HTTP_FORBIDDEN);
        }

        $this->em->remove($comment);
        $this->em->flush();

        return new JsonResponse(['message' => 'Comment deleted successfully'], JsonResponse::HTTP_OK);
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

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu($data['contenu']);
        $commentaire->setDateCreation(new \DateTime());
        $commentaire->setFilm($film);
        $commentaire->setUtilisateur($user);

        $this->em->persist($commentaire);
        $this->em->flush();

        return new JsonResponse(['message' => 'Comment added successfully'], JsonResponse::HTTP_CREATED);
    }
}
