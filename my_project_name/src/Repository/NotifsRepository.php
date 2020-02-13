<?php

namespace App\Repository;

use App\Entity\Notifs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Notifs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notifs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notifs[]    findAll()
 * @method Notifs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotifsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notifs::class);
    }

    // /**
    //  * @return Notifs[] Returns an array of Notifs objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notifs
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
