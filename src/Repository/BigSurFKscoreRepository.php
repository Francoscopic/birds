<?php

namespace App\Repository;

use App\Entity\BigSurFKscore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BigSurFKscore>
 *
 * @method BigSurFKscore|null find($id, $lockMode = null, $lockVersion = null)
 * @method BigSurFKscore|null findOneBy(array $criteria, array $orderBy = null)
 * @method BigSurFKscore[]    findAll()
 * @method BigSurFKscore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BigSurFKscoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BigSurFKscore::class);
    }

    public function save(BigSurFKscore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BigSurFKscore $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BigSurFKscore[] Returns an array of BigSurFKscore objects
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

//    public function findOneBySomeField($value): ?BigSurFKscore
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
