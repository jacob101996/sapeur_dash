<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
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
    private $civility;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_room;

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
     * @ORM\Column(type="string", length=255)
     */
    private $partner_residence;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_fonction;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datenaissance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_enterprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contact_enterprise;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $partner_nregistre;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param mixed $civility
     */
    public function setCivility($civility): void
    {
        $this->civility = $civility;
    }

    /**
     * @return mixed
     */
    public function getTypeRoom()
    {
        return $this->type_room;
    }

    /**
     * @param mixed $type_room
     */
    public function setTypeRoom($type_room): void
    {
        $this->type_room = $type_room;
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

    /**
     * @return mixed
     */
    public function getPartnerResidence()
    {
        return $this->partner_residence;
    }

    /**
     * @param mixed $partner_residence
     */
    public function setPartnerResidence($partner_residence): void
    {
        $this->partner_residence = $partner_residence;
    }

    /**
     * @return mixed
     */
    public function getPartnerFonction()
    {
        return $this->partner_fonction;
    }

    /**
     * @param mixed $partner_fonction
     */
    public function setPartnerFonction($partner_fonction): void
    {
        $this->partner_fonction = $partner_fonction;
    }



    public function getDatenaissance(): ?\DateTimeInterface
    {
        return $this->datenaissance;
    }

    public function setDatenaissance(?\DateTimeInterface $datenaissance): self
    {
        $this->datenaissance = $datenaissance;

        return $this;
    }

    public function getIsEnterprise(): ?bool
    {
        return $this->is_enterprise;
    }

    public function setIsEnterprise(bool $is_enterprise): self
    {
        $this->is_enterprise = $is_enterprise;

        return $this;
    }

    public function getContactEnterprise(): ?string
    {
        return $this->contact_enterprise;
    }

    public function setContactEnterprise(string $contact_enterprise): self
    {
        $this->contact_enterprise = $contact_enterprise;

        return $this;
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

    public function getPartnerNregistre(): ?string
    {
        return $this->partner_nregistre;
    }

    public function setPartnerNregistre(?string $partner_nregistre): self
    {
        $this->partner_nregistre = $partner_nregistre;

        return $this;
    }
}
