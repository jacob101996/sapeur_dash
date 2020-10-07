<?php

namespace App\Entity;

use App\Repository\TypeRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeRoomRepository::class)
 */
class TypeRoom
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle_fr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle_en;


    public function __construct()
    {
        $this->partners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleFr(): ?string
    {
        return $this->libelle_fr;
    }

    public function setLibelleFr(string $libelle_fr): self
    {
        $this->libelle_fr = $libelle_fr;

        return $this;
    }

    public function getLibelleEn(): ?string
    {
        return $this->libelle_en;
    }

    public function setLibelleEn(string $libelle_en): self
    {
        $this->libelle_en = $libelle_en;

        return $this;
    }
}
