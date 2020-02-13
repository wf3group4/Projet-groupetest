<?php

namespace App\Repository;

use App\Entity\Annonces;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

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

    public function searchByAnnonce($search, $prix)
    {
        return $this->createQueryBuilder('u')
            ->orWhere('u.titre LIKE :titre')
            ->orWhere('u.prix LIKE :prix')
            ->setParameters(new ArrayCollection(array(
                  new Parameter('titre', "%$search%"),
                  new Parameter('prix', "$prix")
                        )))


            ->getQuery()
            ->getResult()
            ;
    }
    

    /*
    public function findOneBySomeField($value): ?Annonces
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
