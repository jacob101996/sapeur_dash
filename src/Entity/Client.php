<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clt_firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clt_lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clt_phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clt_residence;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $clt_birth_day;

    /**
     * @ORM\Column(type="datetime")
     */
    private $client_at;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCltFirstname(): ?string
    {
        return $this->clt_firstname;
    }

    public function setCltFirstname(string $clt_firstname): self
    {
        $this->clt_firstname = $clt_firstname;

        return $this;
    }

    public function getCltLastname(): ?string
    {
        return $this->clt_lastname;
    }

    public function setCltLastname(string $clt_lastname): self
    {
        $this->clt_lastname = $clt_lastname;

        return $this;
    }

    public function getCltPhone(): ?string
    {
        return $this->clt_phone;
    }

    public function setCltPhone(string $clt_phone): self
    {
        $this->clt_phone = $clt_phone;

        return $this;
    }

    public function getCltResidence(): ?string
    {
        return $this->clt_residence;
    }

    public function setCltResidence(string $clt_residence): self
    {
        $this->clt_residence = $clt_residence;

        return $this;
    }

    public function getCltBirthDay(): ?\DateTimeInterface
    {
        return $this->clt_birth_day;
    }

    public function setCltBirthDay(\DateTimeInterface $clt_birth_day): self
    {
        $this->clt_birth_day = $clt_birth_day;

        return $this;
    }

    public function getClientAt(): ?\DateTimeInterface
    {
        return $this->client_at;
    }

    public function setClientAt(\DateTimeInterface $client_at): self
    {
        $this->client_at = $client_at;

        return $this;
    }
}
