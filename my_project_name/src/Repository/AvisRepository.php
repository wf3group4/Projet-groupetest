<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Avis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avis[]    findAll()
 * @method Avis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    public function getUserAvis($user)
    {
        return $this->createQueryBuilder('avis')
        ->andWhere('avis.users = :user')
            ->setParameter('user', $user)
        ->andWhere('avis.rgpd = 1')
        ->getQuery()
        ->getResult()
        ;
    }

//    public function moyenne()
//    {
//        $query = 'SELECT avis.users_id, SUM(avis.note)/COUNT(avis.note) FROM avis GROUP BY users_id';
////        return $this->createNativeQuery($query)
////            ->setParameter('note')
////
////            ->getResult()
////            ;
////        return $this->createQueryBuilder('u')
////            ->select(['users_id, SUM(note)/COUNT(note)'])
////            ;
//    }
    // /**
    //  * @return Avis[] Returns an array of Avis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Avis
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
