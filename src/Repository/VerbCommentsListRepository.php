<?php

namespace App\Repository;

use App\Entity\VerbCommentsList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VerbCommentsList>
 *
 * @method VerbCommentsList|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerbCommentsList|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerbCommentsList[]    findAll()
 * @method VerbCommentsList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbCommentsListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerbCommentsList::class);
    }

    public function save(VerbCommentsList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(VerbCommentsList $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VerbCommentsList[] Returns an array of VerbCommentsList objects
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

//    public function findOneBySomeField($value): ?VerbCommentsList
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
