<?php

namespace App\Entity;

use App\Repository\SavRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SavRepository::class)
 */
class Sav
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="savs")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nfacture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mnt_ttc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ref_cmd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sav_at;

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

    public function getNfacture(): ?string
    {
        return $this->nfacture;
    }

    public function setNfacture(string $nfacture): self
    {
        $this->nfacture = $nfacture;

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

    public function getRefCmd(): ?string
    {
        return $this->ref_cmd;
    }

    public function setRefCmd(string $ref_cmd): self
    {
        $this->ref_cmd = $ref_cmd;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getSavAt(): ?\DateTimeInterface
    {
        return $this->sav_at;
    }

    public function setSavAt(\DateTimeInterface $sav_at): self
    {
        $this->sav_at = $sav_at;

        return $this;
    }
}
