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

    public function findSansLesArchives(){
        //dans la table sortie
        $qb=$this->createQueryBuilder('s');
        //recherche où l'état n'est pas archivé
        $qb->andWhere('s.etat<7');

        $query = $qb->getQuery();
        return $query->getResult();
    }


    /**
     * requête personnalisée selon les données de recherches donnés en paramètre
     * @param $campus
     * @param $text
     * @param $dateDebut
     * @param $dateFin
     * @param $organise
     * @param $inscrit
     * @param $nonInscrit
     * @param $sortiesPassees
     * @param $user
     * @return int|mixed|string
     */
    public function findByPerso($campus, $text, $dateDebut, $dateFin, $organise, $inscrit, $nonInscrit, $sortiesPassees, $user )
    {
        $qb =$this->createQueryBuilder('s');

        //recherche où l'état n'est pas archivé
        $qb->andWhere('s.etat<7');
        if($campus != null){
            $qb ->andWhere('s.campus = :campus')
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
            $qb ->andWhere(':inscrit MEMBER OF s.users')
                ->setParameter('inscrit', $user);
        }
        if($nonInscrit){
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

    public function findSortiesParticipeesBy($user){
        $qb =$this->createQueryBuilder('s');
        $qb->where(':inscrit MEMBER OF s.users')
            ->setParameter('inscrit', $user);
        $query = $qb->getQuery();
        return $query->execute();
    }
}
