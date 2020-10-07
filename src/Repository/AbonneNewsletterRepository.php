<?php

namespace App\Repository;

use App\Entity\AbonneNewsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AbonneNewsletter|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbonneNewsletter|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbonneNewsletter[]    findAll()
 * @method AbonneNewsletter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonneNewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbonneNewsletter::class);
    }

    // /**
    //  * @return AbonneNewsletter[] Returns an array of AbonneNewsletter objects
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
    public function findOneBySomeField($value): ?AbonneNewsletter
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
