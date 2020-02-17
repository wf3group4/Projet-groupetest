<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Lastname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="smallint")
     */
    private $active;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar = 'images/imageDefault.jpg';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Portfolio", mappedBy="user", orphanRemoval=true)
     */
    private $portfolios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Annonces", mappedBy="user")
     */
    private $annonces;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Avis", mappedBy="users")
     */
    private $avis;
    
    /** 
     * @ORM\ManyToMany(targetEntity="App\Entity\Annonces", mappedBy="user_postulant")
     */
    private $annonces_postule;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notifs", mappedBy="user", orphanRemoval=true)
     */
    private $notifs;

    /*
     * @ORM\OneToMany(targetEntity="App\Entity\Signalement", mappedBy="user")
     */
    private $signalements;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commission;

    /**
     * @ORM\Column(type="integer")
     */
    private $vues;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Annonces", mappedBy="prestataire")
     */
    private $annonces_prestataire;



    public function __construct()
    {
        $this->portfolios = new ArrayCollection();
        $this->annonces = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->annonces_postule = new ArrayCollection();
        $this->notifs = new ArrayCollection();
        $this->signalements = new ArrayCollection();
        $this->annonces_prestataire = new ArrayCollection();
    }

    public function getMoyenne()
    {
        $moyenne = 0;
        $note = 0;
        $avis = $this->getAvis();
        $nbAvis = count($avis);
        if ($nbAvis) {
            foreach ($avis as $avi) {
                $note = $note + $avi->getNote();
            }
            $moyenne = $note / $nbAvis;
        }

        return $moyenne;
    }




    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_PUBLISHER
        $roles[] = 'ROLE_PUBLISHER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRoles($role)
    {
        if (in_array($role, $this->getRoles())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->Lastname;
    }

    public function setLastname(string $Lastname): self
    {
        $this->Lastname = $Lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }



    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Portfolio[]
     */
    public function getPortfolios(): Collection
    {
        return $this->portfolios;
    }

    public function addPortfolio(Portfolio $portfolio): self
    {
        if (!$this->portfolios->contains($portfolio)) {
            $this->portfolios[] = $portfolio;
            $portfolio->setUser($this);
        }

        return $this;
    }

    public function removePortfolio(Portfolio $portfolio): self
    {
        if ($this->portfolios->contains($portfolio)) {
            $this->portfolios->removeElement($portfolio);
            // set the owning side to null (unless already changed)
            if ($portfolio->getUser() === $this) {
                $portfolio->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Annonces[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonces $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonces $annonce): self
    {
        if ($this->annonces->contains($annonce)) {
            $this->annonces->removeElement($annonce);
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setUsers($this);
        }
        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->contains($avi)) {
            $this->avis->removeElement($avi);
            // set the owning side to null (unless already changed)
            if ($avi->getUsers() === $this) {
                $avi->setUsers(null);
            }
        }
        return $this;
    }

     /**       
     * @return Collection|Annonces[]
     */
    public function getAnnoncesPostule(): Collection
    {
        return $this->annonces_postule;
    }

    public function addAnnoncesPostule(Annonces $annoncesPostule): self
    {
        if (!$this->annonces_postule->contains($annoncesPostule)) {
            $this->annonces_postule[] = $annoncesPostule;
            $annoncesPostule->addUserPostulant($this);
        }

        return $this;
    }

    public function removeAnnoncesPostule(Annonces $annoncesPostule): self
    {
        if ($this->annonces_postule->contains($annoncesPostule)) {
            $this->annonces_postule->removeElement($annoncesPostule);
            $annoncesPostule->removeUserPostulant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Notifs[]
     */
    public function getNotifs(): Collection
    {
        return $this->notifs;
    }

    public function addNotif(Notifs $notif): self
    {
        if (!$this->notifs->contains($notif)) {
            $this->notifs[] = $notif;
            $notif->setUser($this);
        }
    }
        
    /*
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
            $signalement->setUser($this);
    
        }
    }
    
    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->contains($signalement)) {
            $this->signalements->removeElement($signalement);
            // set the owning side to null (unless already changed)
            if ($signalement->getUser() === $this) {
                $signalement->setUser(null);

            }
        }
    }  
    
    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(?float $commission): self
    {
        $this->commission = $commission;

        return $this;
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

    /**
     * @return Collection|Annonces[]
     */
    public function getAnnoncesPrestataire(): Collection
    {
        return $this->annonces_prestataire;
    }

    public function addAnnoncesPrestataire(Annonces $annoncesPrestataire): self
    {
        if (!$this->annonces_prestataire->contains($annoncesPrestataire)) {
            $this->annonces_prestataire[] = $annoncesPrestataire;
            $annoncesPrestataire->setPrestataire($this);
        }

        return $this;
    }

    public function removeNotif(Notifs $notif): self
    {
        if ($this->notifs->contains($notif)) {
            $this->notifs->removeElement($notif);
            // set the owning side to null (unless already changed)
            if ($notif->getUser() === $this) {
                $notif->setUser(null);

            }
        }
    }
    
    public function removeAnnoncesPrestataire(Annonces $annoncesPrestataire): self
    {
        if ($this->annonces_prestataire->contains($annoncesPrestataire)) {
            $this->annonces_prestataire->removeElement($annoncesPrestataire);
            // set the owning side to null (unless already changed)
            if ($annoncesPrestataire->getPrestataire() === $this) {
                $annoncesPrestataire->setPrestataire(null);
            }
        }

        return $this;
    }

}
