<?php

namespace App\Repository;

use App\Entity\Biph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Biph>
 */
class BiphRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Biph::class);
    }

        /**
         * @return Biph[] Returns an array of Biph objects
         */
        public function findByExampleField($value): array
        {
            return $this->createQueryBuilder('b')
                ->andWhere('b.exampleField = :val')
                ->setParameter('val', $value)
                ->orderBy('b.id', 'ASC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult()
            ;
        }

        public function findOneBySomeField($value): ?Biph
        {
            return $this->createQueryBuilder('b')
                ->andWhere('b.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
