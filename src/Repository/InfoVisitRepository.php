<?php

namespace App\Repository;

use App\Entity\InfoVisit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoVisit>
 *
 * @method InfoVisit|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoVisit|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoVisit[]    findAll()
 * @method InfoVisit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoVisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoVisit::class);
    }

//    /**
//     * @return InfoVisit[] Returns an array of InfoVisit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InfoVisit
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
