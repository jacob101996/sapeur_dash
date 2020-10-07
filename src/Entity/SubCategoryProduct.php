<?php

namespace App\Entity;

use App\Repository\SubCategoryProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubCategoryProductRepository::class)
 */
class SubCategoryProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryProduct::class, inversedBy="sub_category")
     */
    private $category_product;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle_fr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle_en;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="sub_category")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryProduct(): ?CategoryProduct
    {
        return $this->category_product;
    }

    public function setCategoryProduct(?CategoryProduct $category_product): self
    {
        $this->category_product = $category_product;

        return $this;
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

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSubCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getSubCategory() === $this) {
                $product->setSubCategory(null);
            }
        }

        return $this;
    }
}
