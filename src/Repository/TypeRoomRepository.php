<?php

namespace App\Repository;

use App\Entity\TypeRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeRoom[]    findAll()
 * @method TypeRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeRoom::class);
    }

    // /**
    //  * @return TypeRoom[] Returns an array of TypeRoom objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeRoom
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
