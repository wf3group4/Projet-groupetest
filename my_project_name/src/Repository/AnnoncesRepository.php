<?php

namespace App\Repository;

use App\Entity\Annonces;
use App\Entity\Notifs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Annonces|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonces|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonces[]    findAll()
 * @method Annonces[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnoncesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonces::class);
    }

     /**
      * @return Annonces[] Returns an array of Annonces objects
      */
    
    public function getUserAnnonces($user)
    {
        return $this->findBy(
            array(
                'user' => $user, 
                'active' => [1,2,3,4]
            )
        )
        ;
    }

    /**
      * @return LastAnnonces[] Returns an array of the last Annonces 
      */

    public function getLastAnnonces($max){
        return $this->findBy(
            array(
                'active' => 1
            ),
            array(
                'date_creation' => 'DESC'
            ),
            $max
        )
        ;
    }
}
