<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PartnerRepository::class)
 */
class Partner
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
    private $partner_firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 8, max = 8, minMessage="Numero de telephone invalide")
     */
    private $partner_phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_shop_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_nroom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $partner_email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $partner_site;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $siege_social;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="partners")
     */
    private $activity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datecrea;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="partner")
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=Civility::class, inversedBy="partners")
     */
    private $civility;

    /**
     * @ORM\ManyToOne(targetEntity=TypeRoom::class, inversedBy="partners")
     */
    private $type_room;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPartnerFirstname()
    {
        return $this->partner_firstname;
    }

    /**
     * @param mixed $partner_firstname
     */
    public function setPartnerFirstname($partner_firstname): void
    {
        $this->partner_firstname = $partner_firstname;
    }

    /**
     * @return mixed
     */
    public function getPartnerLastname()
    {
        return $this->partner_lastname;
    }

    /**
     * @param mixed $partner_lastname
     */
    public function setPartnerLastname($partner_lastname): void
    {
        $this->partner_lastname = $partner_lastname;
    }

    /**
     * @return mixed
     */
    public function getPartnerPhone()
    {
        return $this->partner_phone;
    }

    /**
     * @param mixed $partner_phone
     */
    public function setPartnerPhone($partner_phone): void
    {
        $this->partner_phone = $partner_phone;
    }

    /**
     * @return mixed
     */
    public function getPartnerCode()
    {
        return $this->partner_code;
    }

    /**
     * @param mixed $partner_code
     */
    public function setPartnerCode($partner_code): void
    {
        $this->partner_code = $partner_code;
    }

    /**
     * @return mixed
     */
    public function getPartnerShopName()
    {
        return $this->partner_shop_name;
    }

    /**
     * @param mixed $partner_shop_name
     */
    public function setPartnerShopName($partner_shop_name): void
    {
        $this->partner_shop_name = $partner_shop_name;
    }

    /**
     * @return mixed
     */
    public function getPartnerNroom()
    {
        return $this->partner_nroom;
    }

    /**
     * @param mixed $partner_nroom
     */
    public function setPartnerNroom($partner_nroom): void
    {
        $this->partner_nroom = $partner_nroom;
    }

    public function getPartnerEmail(): ?string
    {
        return $this->partner_email;
    }

    public function setPartnerEmail(string $partner_email): self
    {
        $this->partner_email = $partner_email;

        return $this;
    }

    public function getPartnerSite(): ?string
    {
        return $this->partner_site;
    }

    public function setPartnerSite(?string $partner_site): self
    {
        $this->partner_site = $partner_site;

        return $this;
    }

    public function getSiegeSocial(): ?string
    {
        return $this->siege_social;
    }

    public function setSiegeSocial(string $siege_social): self
    {
        $this->siege_social = $siege_social;

        return $this;
    }


    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getDatecrea(): ?\DateTimeInterface
    {
        return $this->datecrea;
    }

    public function setDatecrea(\DateTimeInterface $datecrea): self
    {
        $this->datecrea = $datecrea;

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
            $product->setPartner($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPartner() === $this) {
                $product->setPartner(null);
            }
        }

        return $this;
    }

    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

    public function setCivility(?Civility $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getTypeRoom(): ?TypeRoom
    {
        return $this->type_room;
    }

    public function setTypeRoom(?TypeRoom $type_room): self
    {
        $this->type_room = $type_room;

        return $this;
    }

}
