<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Image>
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function findAllByConsensus(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.consensus >= 0.8')
            ->getQuery()
            ->getResult();
    }

    public function findByTypeAndConsensus(string $type, bool $isPositive): array
    {
        return $this->createQueryBuilder('i')
         //   ->where('i.consensus >= 0.8')
            ->where('i.type = :type')
            ->andWhere('i.isPositive = :isPositive')
            ->setParameter('type', $type)
            ->setParameter('isPositive', $isPositive)
            ->getQuery()
            ->getResult();
    }
}
