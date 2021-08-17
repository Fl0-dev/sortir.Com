<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByPerso($Campus,$text,$dateDebut,$dateFin)
    {
        $qb =$this->createQueryBuilder('s')
            ->where('s.campus_id = :campus.id')
            ->andWhere('s.nom = :text')
            ->andWhere('date_heure_debut> :dateDebut')
            ->andWhere('date_limite_inscription< :dateFin')
            ->setParameter('dateFin', $dateFin)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('text', $text)
            ->setParameter('campus', $Campus)
            ->orderBy('date_limite_inscription','ASC');

        $query = $qb->getQuery();
        $query->setMaxResults(50);

        return $query->execute();
    }
}
