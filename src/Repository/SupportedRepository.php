<?php

namespace App\Repository;

use App\Entity\Supported;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Supported>
 *
 * @method Supported|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supported|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supported[]    findAll()
 * @method Supported[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupportedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supported::class);
    }

//    /**
//     * @return Supported[] Returns an array of Supported objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Supported
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
