<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Users;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnoncesRepository")
 */
class Annonces
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_limite;

    /**
     * @ORM\Column(type="smallint")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Users", inversedBy="annonces_postule")
     */
    private $user_postulant;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tags", inversedBy="annonces")
     */
    private $tag;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $prix;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Signalement", mappedBy="annonce")
     */
    private $signalements;

    /**
     * @ORM\Column(type="integer")
     */
    private $vues;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="annonces_prestataire")
     */
    private $prestataire;




    public function __construct()
    {
        $this->user_postulant = new ArrayCollection();
        $this->tag = new ArrayCollection();
        $this->signalements = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateLimite(): ?\DateTimeInterface
    {
        return $this->date_limite;
    }

    public function setDateLimite(?\DateTimeInterface $date_limite): self
    {
        $this->date_limite = $date_limite;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

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

    /**
     * @return Collection|Users[]
     */
    public function getUserPostulant(): Collection
    {
        return $this->user_postulant;
    }

    public function addUserPostulant(Users $userPostulant): self
    {
        if (!$this->user_postulant->contains($userPostulant)) {
            $this->user_postulant[] = $userPostulant;
        }

        return $this;
    }

    public function removeUserPostulant(Users $userPostulant): self
    {
        if ($this->user_postulant->contains($userPostulant)) {
            $this->user_postulant->removeElement($userPostulant);
        }

        return $this;
    }

    /**
     * @return Collection|Tags[]
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tags $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        if ($this->tag->contains($tag)) {
            $this->tag->removeElement($tag);
        }

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }


    /**
     * @return Collection|Signalement[]
     */
    public function getSignalements(): Collection
    {
        return $this->signalements;
    }

    public function addSignalement(Signalement $signalement): self
    {
        if (!$this->signalements->contains($signalement)) {
            $this->signalements[] = $signalement;
            $signalement->setAnnonce($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->contains($signalement)) {
            $this->signalements->removeElement($signalement);
            // set the owning side to null (unless already changed)
            if ($signalement->getAnnonce() === $this) {
                $signalement->setAnnonce(null);
            }
        }
    }
    public function getVues(): ?int
    {
        return $this->vues;
    }

    public function setVues(int $vues): self
    {
        $this->vues = $vues;

        return $this;
    }

    public function getPrestataire(): ?Users
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Users $prestataire): self
    {
        $this->prestataire = $prestataire;

        return $this;
    }


}
