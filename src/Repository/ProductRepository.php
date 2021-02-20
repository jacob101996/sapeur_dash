<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */

    public function findProductByCat($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProductByCatAndSub($cat, $subCat)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :val1')
            ->andWhere('p.sub_category = :val2')
            ->setParameter('val1', $cat)
            ->setParameter('val2', $subCat)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findRandomProd(){
        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'p');
        $sql = "SELECT * FROM product ORDER BY RAND() LIMIT 2";
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->getResult();
    }

    public function findRandomProdMan(){
        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'p');
        $sql = "SELECT * FROM product WHERE category_id =:cat ORDER BY RAND() LIMIT 8";
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->setParameter("cat", 1)
            ->getResult();
    }

    public function findRandomProdWife(){
        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'p');
        $sql = "SELECT * FROM product WHERE category_id =:cat ORDER BY RAND() LIMIT 8";
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->setParameter("cat", 2)
            ->getResult();
    }

    public function findRandomProdEnfant(){
        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'p');
        $sql = "SELECT * FROM product WHERE category_id =:cat ORDER BY RAND() LIMIT 8";
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->setParameter("cat", 3)
            ->getResult();
    }

    public function findRandomProdSport(){
        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'p');
        $sql = "SELECT * FROM product WHERE category_id =:cat ORDER BY RAND() LIMIT 8";
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->setParameter("cat", 4)
            ->getResult();
    }

    public function findManiProductItems(){
        $rsm = new \Doctrine\ORM\Query\ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Product::class, 'p');
        $sql = "SELECT * FROM product  ORDER BY RAND() LIMIT 20";
        return $this->getEntityManager()->createNativeQuery($sql, $rsm)
            ->getResult();
    }
}
