<?php

namespace App\Repository;

use App\Entity\BigSurSubscribes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BigSurSubscribes>
 *
 * @method BigSurSubscribes|null find($id, $lockMode = null, $lockVersion = null)
 * @method BigSurSubscribes|null findOneBy(array $criteria, array $orderBy = null)
 * @method BigSurSubscribes[]    findAll()
 * @method BigSurSubscribes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BigSurSubscribesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BigSurSubscribes::class);
    }

    public function save(BigSurSubscribes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BigSurSubscribes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BigSurSubscribes[] Returns an array of BigSurSubscribes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BigSurSubscribes
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
