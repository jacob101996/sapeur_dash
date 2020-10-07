<?php

namespace App\Repository;

use App\Entity\PanierProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PanierProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method PanierProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method PanierProduct[]    findAll()
 * @method PanierProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanierProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PanierProduct::class);
    }

    // /**
    //  * @return PanierProduct[] Returns an array of PanierProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PanierProduct
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
