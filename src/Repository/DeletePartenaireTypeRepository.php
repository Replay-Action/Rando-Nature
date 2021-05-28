<?php

namespace App\Repository;

use App\Entity\DeletePartenaireType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeletePartenaireType|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeletePartenaireType|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeletePartenaireType[]    findAll()
 * @method DeletePartenaireType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeletePartenaireTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeletePartenaireType::class);
    }

    // /**
    //  * @return DeletePartenaireType[] Returns an array of DeletePartenaireType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeletePartenaireType
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
