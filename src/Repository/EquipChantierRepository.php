<?php

namespace App\Repository;

use App\Entity\EquipChantier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipChantier>
 *
 * @method EquipChantier|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipChantier|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipChantier[]    findAll()
 * @method EquipChantier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipChantierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipChantier::class);
    }

//    /**
//     * @return EquipChantier[] Returns an array of EquipChantier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EquipChantier
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
