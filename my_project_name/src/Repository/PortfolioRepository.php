<?php

namespace App\Repository;

use App\Entity\Portfolio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Portfolio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portfolio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portfolio[]    findAll()
 * @method Portfolio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortfolioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portfolio::class);
    }

    /**
     * @return Portfolio[] Returns an array of Portfolio objects
     */
    public function getUserPortfolios($user)
    {
        return $this->createQueryBuilder('portfolio')
            ->andWhere('portfolio.user = :user')
                ->setParameter('user', $user)
            ->andWhere('portfolio.img_url IS NOT NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    

    /**
     * @return LastPortfolios[] Returns an array of the last Arts posted
     */
    public function getLastPortfolios($max)
    {
        return $this->createQueryBuilder('portfolio')
        ->andWhere('portfolio.img_url IS NOT NULL')
        ->orderBy('portfolio.id', 'DESC')
        ->setMaxResults($max)
        ->getQuery()
        ->getResult()
    ;
    }

    public function getUserLastPortfolio($user)
    {
        return $this->createQueryBuilder('portfolio')
            ->andWhere('portfolio.user = :user')
                ->setParameter('user', $user)
            ->andWhere('portfolio.img_url IS NOT NULL')
            ->orderBy('portfolio.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getUserLiens($user)
    {
        return $this->createQueryBuilder('portfolio')
        ->andWhere('portfolio.user = :user')
        ->setParameter('user', $user)
        ->andWhere('portfolio.liens IS NOT NULL')
        ->getQuery()
        ->getResult()
        ;
    }

  
    

    /*
    public function findOneBySomeField($value): ?Portfolio
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
