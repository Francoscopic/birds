<?php

namespace App\Repository;

use App\Entity\VerbUnlikes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VerbUnlikes>
 *
 * @method VerbUnlikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerbUnlikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerbUnlikes[]    findAll()
 * @method VerbUnlikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbUnlikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerbUnlikes::class);
    }

    public function save(VerbUnlikes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VerbUnlikes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VerbUnlikes[] Returns an array of VerbUnlikes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VerbUnlikes
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
