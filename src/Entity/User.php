<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $user_at;

    /**
     * @ORM\ManyToOne(targetEntity=Civility::class, inversedBy="partners")
     */
    private $civility;

    /**
     * @ORM\ManyToOne(targetEntity=TypeRoom::class, inversedBy="partners")
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
    private $partner_room_1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_room_2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $partner_residence;


    /**
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="user")
     */
    private $commands;

    /**
     * @ORM\OneToMany(targetEntity=Sav::class, mappedBy="user")
     */
    private $savs;


    public function __construct()
    {
        $this->commands = new ArrayCollection();
        $this->savs = new ArrayCollection();
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
    public function getPartnerRoom1()
    {
        return $this->partner_room_1;
    }

    /**
     * @param mixed $partner_room_1
     */
    public function setPartnerRoom1($partner_room_1): void
    {
        $this->partner_room_1 = $partner_room_1;
    }

    /**
     * @return mixed
     */
    public function getPartnerRoom2()
    {
        return $this->partner_room_2;
    }

    /**
     * @param mixed $partner_room_2
     */
    public function setPartnerRoom2($partner_room_2): void
    {
        $this->partner_room_2 = $partner_room_2;
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


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUserAt(): ?\DateTimeInterface
    {
        return $this->user_at;
    }

    public function setUserAt(\DateTimeInterface $user_at): self
    {
        $this->user_at = $user_at;

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setUser($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->contains($command)) {
            $this->commands->removeElement($command);
            // set the owning side to null (unless already changed)
            if ($command->getUser() === $this) {
                $command->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sav[]
     */
    public function getSavs(): Collection
    {
        return $this->savs;
    }

    public function addSav(Sav $sav): self
    {
        if (!$this->savs->contains($sav)) {
            $this->savs[] = $sav;
            $sav->setUser($this);
        }

        return $this;
    }

    public function removeSav(Sav $sav): self
    {
        if ($this->savs->contains($sav)) {
            $this->savs->removeElement($sav);
            // set the owning side to null (unless already changed)
            if ($sav->getUser() === $this) {
                $sav->setUser(null);
            }
        }

        return $this;
    }
}
