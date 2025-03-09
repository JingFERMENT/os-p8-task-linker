<?php

namespace App\Entity;

use App\Repository\StatutRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutRepository::class)]
class Statut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statutName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatutName(): ?string
    {
        return $this->statutName;
    }

    public function setStatutName(string $statutName): static
    {
        $this->statutName = $statutName;

        return $this;
    }
}
