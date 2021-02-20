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
     * @ORM\Column(type="string", length=255)
     */
    private $ref_cmd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $delivery_location;

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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_clt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tel_clt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buyed_by;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_delivery;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $pay_id;

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

    public function getNameClt(): ?string
    {
        return $this->name_clt;
    }

    public function setNameClt(string $name_clt): self
    {
        $this->name_clt = $name_clt;

        return $this;
    }

    public function getTelClt(): ?string
    {
        return $this->tel_clt;
    }

    public function setTelClt(string $tel_clt): self
    {
        $this->tel_clt = $tel_clt;

        return $this;
    }

    public function getBuyedBy(): ?string
    {
        return $this->buyed_by;
    }

    public function setBuyedBy(string $buyed_by): self
    {
        $this->buyed_by = $buyed_by;

        return $this;
    }

    public function getDateDelivery(): ?\DateTimeInterface
    {
        return $this->date_delivery;
    }

    public function setDateDelivery(\DateTimeInterface $date_delivery): self
    {
        $this->date_delivery = $date_delivery;

        return $this;
    }

    public function getPayId(): ?int
    {
        return $this->pay_id;
    }

    public function setPayId(?int $pay_id): self
    {
        $this->pay_id = $pay_id;

        return $this;
    }

}
