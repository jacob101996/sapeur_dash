<?php

namespace App\Entity;

use App\Repository\TypeProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeProductRepository::class)
 */
class TypeProduct
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
     * @ORM\OneToMany(targetEntity=SubCategoryProduct::class, mappedBy="typeProduct")
     */
    private $sub_category;

    public function __construct()
    {
        $this->sub_category = new ArrayCollection();
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

    /**
     * @return Collection|SubCategoryProduct[]
     */
    public function getSubCategory(): Collection
    {
        return $this->sub_category;
    }

    public function addSubCategory(SubCategoryProduct $subCategory): self
    {
        if (!$this->sub_category->contains($subCategory)) {
            $this->sub_category[] = $subCategory;
            $subCategory->setTypeProduct($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategoryProduct $subCategory): self
    {
        if ($this->sub_category->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getTypeProduct() === $this) {
                $subCategory->setTypeProduct(null);
            }
        }

        return $this;
    }
}
