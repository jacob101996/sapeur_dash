<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SubCategoryProduct::class, inversedBy="products")
     */
    private $sub_category;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryProduct::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $product_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $product_price;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $product_description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_size;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_color;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $product_reduction;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $product_nb_cmd;

    /**
     * @ORM\Column(type="integer")
     */
    private $product_stock;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_ref;

    /**
     * @ORM\Column(type="datetime")
     */
    private $product_at;

    /**
     * @ORM\OneToMany(targetEntity=PanierProduct::class, mappedBy="product")
     */
    private $panierProducts;

    /**
     * @ORM\ManyToMany(targetEntity=Command::class, inversedBy="products")
     */
    private $command;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $product_image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_image1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_image2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product_image3;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $quality_product;

    public function __construct()
    {
        $this->panierProducts = new ArrayCollection();
        $this->command = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubCategory(): ?SubCategoryProduct
    {
        return $this->sub_category;
    }

    public function setSubCategory(?SubCategoryProduct $sub_category): self
    {
        $this->sub_category = $sub_category;

        return $this;
    }

    public function getCategory(): ?CategoryProduct
    {
        return $this->category;
    }

    public function setCategory(?CategoryProduct $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->product_name;
    }

    public function setProductName(string $product_name): self
    {
        $this->product_name = $product_name;

        return $this;
    }

    public function getProductPrice(): ?string
    {
        return $this->product_price;
    }

    public function setProductPrice(string $product_price): self
    {
        $this->product_price = $product_price;

        return $this;
    }

    public function getProductDescription(): ?string
    {
        return $this->product_description;
    }

    public function setProductDescription(string $product_description): self
    {
        $this->product_description = $product_description;

        return $this;
    }

    public function getProductSize(): ?string
    {
        return $this->product_size;
    }

    public function setProductSize(?string $product_size): self
    {
        $this->product_size = $product_size;

        return $this;
    }

    public function getProductColor(): ?string
    {
        return $this->product_color;
    }

    public function setProductColor(?string $product_color): self
    {
        $this->product_color = $product_color;

        return $this;
    }

    public function getProductReduction(): ?int
    {
        return $this->product_reduction;
    }

    public function setProductReduction(?int $product_reduction): self
    {
        $this->product_reduction = $product_reduction;

        return $this;
    }

    public function getProductNbCmd(): ?int
    {
        return $this->product_nb_cmd;
    }

    public function setProductNbCmd(int $product_nb_cmd): self
    {
        $this->product_nb_cmd = $product_nb_cmd;

        return $this;
    }

    public function getProductStock(): ?int
    {
        return $this->product_stock;
    }

    public function setProductStock(int $product_stock): self
    {
        $this->product_stock = $product_stock;

        return $this;
    }

    public function getProductRef(): ?string
    {
        return $this->product_ref;
    }

    public function setProductRef(string $product_ref): self
    {
        $this->product_ref = $product_ref;

        return $this;
    }

    public function getProductAt(): ?\DateTimeInterface
    {
        return $this->product_at;
    }

    public function setProductAt(\DateTimeInterface $product_at): self
    {
        $this->product_at = $product_at;

        return $this;
    }

    /**
     * @return Collection|PanierProduct[]
     */
    public function getPanierProducts(): Collection
    {
        return $this->panierProducts;
    }

    public function addPanierProduct(PanierProduct $panierProduct): self
    {
        if (!$this->panierProducts->contains($panierProduct)) {
            $this->panierProducts[] = $panierProduct;
            $panierProduct->setProduct($this);
        }

        return $this;
    }

    public function removePanierProduct(PanierProduct $panierProduct): self
    {
        if ($this->panierProducts->contains($panierProduct)) {
            $this->panierProducts->removeElement($panierProduct);
            // set the owning side to null (unless already changed)
            if ($panierProduct->getProduct() === $this) {
                $panierProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommand(): Collection
    {
        return $this->command;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->command->contains($command)) {
            $this->command[] = $command;
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->command->contains($command)) {
            $this->command->removeElement($command);
        }

        return $this;
    }

    public function getProductImage(): ?string
    {
        return $this->product_image;
    }

    public function setProductImage(string $product_image): self
    {
        $this->product_image = $product_image;

        return $this;
    }

    public function getProductImage1(): ?string
    {
        return $this->product_image1;
    }

    public function setProductImage1(?string $product_image1): self
    {
        $this->product_image1 = $product_image1;

        return $this;
    }

    public function getProductImage2(): ?string
    {
        return $this->product_image2;
    }

    public function setProductImage2(?string $product_image2): self
    {
        $this->product_image2 = $product_image2;

        return $this;
    }

    public function getProductImage3(): ?string
    {
        return $this->product_image3;
    }

    public function setProductImage3(?string $product_image3): self
    {
        $this->product_image3 = $product_image3;

        return $this;
    }

    public function getQualityProduct(): ?string
    {
        return $this->quality_product;
    }

    public function setQualityProduct(string $quality_product): self
    {
        $this->quality_product = $quality_product;

        return $this;
    }
}
