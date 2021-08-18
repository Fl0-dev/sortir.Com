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
    public function findByPerso($campus,$text,$dateDebut,$dateFin,$organise,$inscrit,$nonInscrit,$sortiesPassees,$user )
    {
        $qb =$this->createQueryBuilder('s');

        //user qui fait la recherche


        if($campus != null){
            $qb ->where('s.campus = :campus')
                ->setParameter('campus', $campus->getId());
        }
        if ($text != null) {
            $qb->andWhere('s.nom LIKE :text')
                ->setParameter('text', '%'.$text.'%');
        }
        if ($dateDebut != null) {
            $qb->andWhere('s.dateHeureDebut > :dateDebut')

                ->setParameter('dateDebut', $dateDebut);
        }
        if ($dateFin != null) {
            $qb->andWhere('s.dateHeureDebut < :dateFin')
                ->setParameter('dateFin', $dateFin);
        }
        if ($organise) {

            $qb ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur',$user);
        }
        if($inscrit){
            //TODO
            $qb ->andWhere(':inscrit MEMBER OF s.users')
                ->setParameter('inscrit', $user);
        }
        if($nonInscrit){
            //TODO
            $qb ->andWhere(':inscrit NOT MEMBER OF s.users')
                ->setParameter('inscrit', $user);
        }
        if($sortiesPassees){
            $qb ->andWhere('s.dateHeureDebut <= :now')
                ->setParameter('now', date('Y-m-d H:i:s') );
        }



        $query = $qb->getQuery();
        $query->setMaxResults(50);

        return $query->execute();
    }
}
