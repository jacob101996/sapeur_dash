<?php

namespace App\Repository;

use App\Entity\Command;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Command|null find($id, $lockMode = null, $lockVersion = null)
 * @method Command|null findOneBy(array $criteria, array $orderBy = null)
 * @method Command[]    findAll()
 * @method Command[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Command::class);
    }

    // /**
    //  * @return Command[] Returns an array of Command objects
    //  */

    public function findByRefCmd($ref)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.ref_cmd = :val')
            ->setParameter('val', $ref)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByPayId($id_pay)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.pay_id = :val')
            ->setParameter('val', $id_pay)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Command
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function dataCommand(){
        $query = $this->getEntityManager()->createQuery(
            "SELECT substring(c.date_delivery,1,7) as date_data, COUNT(c) FROM App\Entity\Command c
                  GROUP BY date_data
                  ORDER BY date_data DESC"
        );
        return $query->setMaxResults(12)->getResult();
    }
}
