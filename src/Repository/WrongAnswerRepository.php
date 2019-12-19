<?php

namespace App\Repository;

use App\Entity\WrongAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WrongAnswer|null find($id, $lockMode = null, $lockVersion = null)
 * @method WrongAnswer|null findOneBy(array $criteria, array $orderBy = null)
 * @method WrongAnswer[]    findAll()
 * @method WrongAnswer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WrongAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WrongAnswer::class);
    }

    // /**
    //  * @return WrongAnswer[] Returns an array of WrongAnswer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WrongAnswer
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
