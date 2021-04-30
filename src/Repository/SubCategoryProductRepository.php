<?php

namespace App\Repository;

use App\Entity\SubCategoryProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubCategoryProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubCategoryProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubCategoryProduct[]    findAll()
 * @method SubCategoryProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubCategoryProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubCategoryProduct::class);
    }

    // /**
    //  * @return SubCategoryProduct[] Returns an array of SubCategoryProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function findOneBySubCat($value1, $value2): ?SubCategoryProduct
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.category_product = :val1')
            ->andWhere('s.libelle_fr = :val2')
            ->setParameter('val1', $value1)
            ->setParameter('val2', $value2)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
