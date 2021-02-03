<?php

namespace App\Repository;

use App\Entity\DocPdf;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocPdf|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocPdf|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocPdf[]    findAll()
 * @method DocPdf[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocPdfRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocPdf::class);
    }

    // /**
    //  * @return DocPdf[] Returns an array of DocPdf objects
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
    public function findOneBySomeField($value): ?DocPdf
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
