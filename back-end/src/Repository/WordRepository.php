<?php

namespace App\Repository;

use App\Entity\Word;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Word>
 */
class WordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Word::class);
    }

    /**
     * Намира всички положителни думи с двубифони.
     *
     * @return Word[]
     */
    public function findPositiveBiphWords(): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.biphCount = 2')
            ->andWhere('w.type = :type')
            ->setParameter('type', 'positive')
            ->getQuery()
            ->getResult();
    }

    /**
     * Намира всички отрицателни думи с двубифони.
     *
     * @return Word[]
     */
    public function findNegativeBiphWords(): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.biphCount = 2')
            ->andWhere('w.type = :type')
            ->setParameter('type', 'negative')
            ->getQuery()
            ->getResult();
    }

    /**
     * Намира всички положителни думи с трибифони.
     *
     * @return Word[]
     */
    public function findPositiveTribiphWords(): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.biphCount = 3')
            ->andWhere('w.type = :type')
            ->setParameter('type', 'positive')
            ->getQuery()
            ->getResult();
    }

    /**
     * Намира всички отрицателни думи с трибифони.
     *
     * @return Word[]
     */
    public function findNegativeTribiphWords(): array
    {
        return $this->createQueryBuilder('w')
            ->where('w.biphCount = 3')
            ->andWhere('w.type = :type')
            ->setParameter('type', 'negative')
            ->getQuery()
            ->getResult();
    }

        /**
     * Намира ID-то на дума по нейната текстова стойност.
     *
     * @param string $wordValue
     * @return int|null
     */
    public function findIdByWordValue(string $wordValue): ?int
    {
        return $this->createQueryBuilder('w')
            ->select('w.id')
            ->where('w.word = :wordValue')
            ->setParameter('wordValue', $wordValue)
            ->getQuery()
            ->getSingleScalarResult();
    }





}