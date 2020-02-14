<?php

namespace App\Repository;

use App\Entity\Signalement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Signalement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Signalement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Signalement[]    findAll()
 * @method Signalement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SignalementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Signalement::class);
    }

    /**
    * @return Signalement[] Returns an array of Signalement objects
    */

    public function getLastWeekSignalements()
    {
        $derniere_semaine = new \DateTime("1 week ago");
        return $this->createQueryBuilder('signalement')
        ->where('signalement.date >= :derniere_semaine')
            ->setParameter('derniere_semaine', $derniere_semaine)
        ->getQuery()
        ->getResult()
        ;

    }

    public function getSignalement($nb)
    {
        // return $this->createQueryBuilder('signalement')
    }


    /*
    public function findOneBySomeField($value): ?Signalement
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
