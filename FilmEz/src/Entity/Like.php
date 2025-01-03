<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $type = 'default_value';

    #[ORM\ManyToOne(inversedBy: 'likes', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Film $film = null;

    #[ORM\ManyToOne(inversedBy: 'likes', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Commentaire $commentaires = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): static
    {
        $this->film = $film;

        return $this;
    }

    public function getCommentaires(): ?Commentaire
    {
        return $this->commentaires;
    }

    public function setCommentaires(?Commentaire $commentaires): static
    {
        $this->commentaires = $commentaires;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->user;
    }

    public function setUtilisateur(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
