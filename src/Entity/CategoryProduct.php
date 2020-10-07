<?php

namespace App\Entity;

use App\Repository\CategoryProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryProductRepository::class)
 */
class CategoryProduct
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

    /**
     * @ORM\OneToMany(targetEntity=SubCategoryProduct::class, mappedBy="category_product")
     */
    private $sub_category;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="category")
     */
    private $products;

    public function __construct()
    {
        $this->sub_category = new ArrayCollection();
        $this->products = new ArrayCollection();
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
            $subCategory->setCategoryProduct($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategoryProduct $subCategory): self
    {
        if ($this->sub_category->contains($subCategory)) {
            $this->sub_category->removeElement($subCategory);
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategoryProduct() === $this) {
                $subCategory->setCategoryProduct(null);
            }
        }

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
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }
}
