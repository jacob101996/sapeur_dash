<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=StatusCommand::class, inversedBy="commands")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commands")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ref_cmd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $delivery_location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date_delivery;

    /**
     * @ORM\Column(type="integer")
     */
    private $trans_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mnt_ht;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $taux_tva;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mnt_ttc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $facture_name;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="command")
     */
    private $products;

    /**
     * @ORM\Column(type="datetime")
     */
    private $command_at;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getStatus(): ?StatusCommand
    {
        return $this->status;
    }

    public function setStatus(?StatusCommand $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRefCmd(): ?string
    {
        return $this->ref_cmd;
    }

    public function setRefCmd(string $ref_cmd): self
    {
        $this->ref_cmd = $ref_cmd;

        return $this;
    }

    public function getDeliveryLocation(): ?string
    {
        return $this->delivery_location;
    }

    public function setDeliveryLocation(string $delivery_location): self
    {
        $this->delivery_location = $delivery_location;

        return $this;
    }

    public function getDateDelivery(): ?string
    {
        return $this->date_delivery;
    }

    public function setDateDelivery(string $date_delivery): self
    {
        $this->date_delivery = $date_delivery;

        return $this;
    }

    public function getTransId(): ?int
    {
        return $this->trans_id;
    }

    public function setTransId(int $trans_id): self
    {
        $this->trans_id = $trans_id;

        return $this;
    }

    public function getMntHt(): ?string
    {
        return $this->mnt_ht;
    }

    public function setMntHt(string $mnt_ht): self
    {
        $this->mnt_ht = $mnt_ht;

        return $this;
    }

    public function getTauxTva(): ?string
    {
        return $this->taux_tva;
    }

    public function setTauxTva(string $taux_tva): self
    {
        $this->taux_tva = $taux_tva;

        return $this;
    }

    public function getMntTtc(): ?string
    {
        return $this->mnt_ttc;
    }

    public function setMntTtc(string $mnt_ttc): self
    {
        $this->mnt_ttc = $mnt_ttc;

        return $this;
    }

    public function getFactureName(): ?string
    {
        return $this->facture_name;
    }

    public function setFactureName(string $facture_name): self
    {
        $this->facture_name = $facture_name;

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
            $product->addCommand($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeCommand($this);
        }

        return $this;
    }

    public function getCommandAt(): ?\DateTimeInterface
    {
        return $this->command_at;
    }

    public function setCommandAt(\DateTimeInterface $command_at): self
    {
        $this->command_at = $command_at;

        return $this;
    }

}
