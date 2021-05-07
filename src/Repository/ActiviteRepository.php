<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Activite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activite[]    findAll()
 * @method Activite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiviteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activite::class);
    }

    #pour lister toutes les activités en une seule requete
    public function findActivites()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->innerJoin('a.organisateur', 'o')
            ->addSelect('o')
            ->innerJoin('a.etat', 'e')
            ->addSelect('e')
            ->innerJoin('a.lieu', 'l')
            ->addSelect('l')
            ->leftJoin('a.users', 'u')
            ->addSelect('u')
            ->orderBy('a.date_activite', 'DESC');

        $query = $qb->getQuery();
        return new Paginator($query);

    }

# pour recuperer les activités dont la date est dépassée et pour changer leur 'etat' en 'finie'
    public function miseajouretat()
    {

        $em = $this->getEntityManager();
        $datejour = new \DateTime();
        $query = $em->createQuery(
            'UPDATE App\Entity\Activite as a
             SET a.etat =2
             WHERE a.date_activite < :date')
            ->setParameter('date', $datejour);

        return $query->getResult();
    }



    public function affichepastille()
    {
        #pour l'affichage de la pastille clignotante dans l'acceuil, on filtre sur les activités ouvertes ou modifiees
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT a
                FROM App\Entity\Activite a
                WHERE a.etat = 1 OR a.etat= 4');

        return $query->getResult();
    }



    public function affichefinie()
    {
        #pour l'affichage de la page de la liste des recaps
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT a
                FROM App\Entity\Activite a
                WHERE a.etat = 2
                ORDER BY a.date_activite');

        return $query->getResult();
    }


    // /**
    //  * @return Activite[] Returns an array of Activite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Activite
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findSearch(SearchData $search): array
    {
        $query = $this
            ->createQueryBuilder('a')
            ->innerJoin('a.organisateur', 'o')
            ->addSelect('o')
            ->innerJoin('a.categorie', 'c')
            ->innerJoin('a.etat', 'e')
            ->addSelect('e')
            ->innerJoin('a.lieu', 'l')
            ->addSelect('l')
            ->leftJoin('a.users', 'u')
            ->addSelect('u')
            ->orderBy('a.date_activite', 'DESC');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('a.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->categories)){
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }


        return $query->getQuery()->getResult();

    }
}
