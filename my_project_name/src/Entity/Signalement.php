<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SignalementRepository")
 */
class Signalement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="signalements")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\annonces", inversedBy="signalements")
     */
    private $annonce;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?users
    {
        return $this->user;
    }

    public function setUser(?users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAnnonce(): ?annonces
    {
        return $this->annonce;
    }

    public function setAnnonce(?annonces $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }
}
