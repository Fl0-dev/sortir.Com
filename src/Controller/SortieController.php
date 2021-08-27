<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\AnnulationType;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/sortie", name = "sortie_")
 */
class SortieController extends AbstractController
{


    /**
     * @Route("/afficher/{id}", name = "afficher")
     */
    public function afficher(Sortie $sortie): Response
    {
        return $this->render('sortie/afficher.html.twig', [
            "sortie" => $sortie
        ]);
    }


    /**
     * @Route("/ajouter", name ="ajouter")
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function ajouter(Request                $request,
                            EtatRepository         $etatRepository,
                            LieuRepository         $lieuRepository,
                            EntityManagerInterface $entityManager): Response
    {
        //récupération de la route pour la redirection dans lieu
        $routeName = $request->get('_route');
        // récupération de l'état
        $etats = $etatRepository->findAll();
        //création de l'ojet sortie
        $sortie = new Sortie();
        //récupération de l'user pour le mettre en organisateur
        $sortie->setOrganisateur($this->getUser());
        //récupération du campus du user
        $campus = $this->getUser()->getCampus();
        $sortie->setCampus($campus);

        //Utilisation du form de sortie
        $form = $this->createForm(SortieFormType::class, $sortie);
        //et envoie du forme en requête
        $form->handleRequest($request);
        //si valide
        if ($form->isSubmitted() && $form->isValid()) {
            //récupération du champ lieu qui ne fait parti du form
            $lieuId = $request->request->get('lieu');
            //recherche du lieu par id
            $lieu=$lieuRepository->find($lieuId);
            //hydratation de la sortie
            $sortie->setLieu($lieu);
            //si l'user veut que la sortie soit créée
            if ($request->get("choix") === "enregistre") {
                $sortie->setEtat($etats[0]);
            }
            //Si l'user veut qu'elle soit publier direct
            if ($request->get("choix") === "publie") {
                $sortie->setEtat($etats[1]);
            }
            //ajout de l'user dans la sortie
            $sortie->addUser($this->getUser());


            //on inscrit en BD
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été crée.');
            return $this->redirectToRoute('accueil');

        }

        return $this->render
        ('sortie/ajouter.html.twig', [
            'route' => $routeName,
            'user' => $this->getUser(),
            'formSortie' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/modifier/", name ="modifier")
     */
    public function modifier(Sortie                 $sortie,
                             Request                $request,
                             EtatRepository         $etatRepository,
                             LieuRepository         $lieuRepository,
                             EntityManagerInterface $entityManager): Response
    {
        //récupération de la route pour la redirection dans lieu
        $routeName = $request->get('_route');
        // récupération de l'état
        $etats = $etatRepository->findAll();
        //Utilisation du form de sortie
        $form = $this->createForm(SortieFormType::class, $sortie);
        //et envoie du forme en requête
        $form->handleRequest($request);
        //si valide
        if ($form->isSubmitted() && $form->isValid()) {
            //récupération du champ lieu qui ne fait parti du form
            $lieuId = $request->request->get('lieu');
            //recherche du lieu par id
            $lieu=$lieuRepository->find($lieuId);
            //hydratation de la sortie
            $sortie->setLieu($lieu);
            //si l'user veut que la sortie soit créée
            if ($request->get("choix") === "enregistre") {
                $sortie->setEtat($etats[0]);
            }
            //Si l'user veut qu'elle soit publier direct
            if ($request->get("choix") === "publie") {
                $sortie->setEtat($etats[1]);
            }
            //on inscrit en BD
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été modifiée.');
            return $this->redirectToRoute('accueil');
        }

        return $this->render
        ('sortie/modifier.html.twig', [
            'sortie' => $sortie,
            'route' => $routeName,
            'formSortie' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name ="supprimer")
     */
    public function supprimer(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {

        if ($sortie->getEtat()->getId() == 1) {
            $entityManager->remove($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie a bien été supprimée.');
        }
        return $this->redirectToRoute("accueil");
    }

    /**
     * si déjà publier, annule la sortie en changeant son état
     * @Route("/{id}/annuler", name="annuler")
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function annuler(Sortie $sortie, EntityManagerInterface $entityManager, Request $request, EtatRepository $etatRepository): Response
    {
        $annulationForm = $this->createForm(AnnulationType::class, $sortie);
        $annulationForm->handleRequest($request);
        if ($annulationForm->isSubmitted() && $annulationForm->isValid()) {


            $etatAnnulee = $etatRepository->find(6);
            $sortie->setEtat($etatAnnulee);
            $entityManager->flush();
            $this->addFlash('warning', 'La sortie a bien été annulée.');
            return $this->redirectToRoute('accueil');
        }

        return $this->render
        ('sortie/annuler.html.twig', [
            'annulationForm' => $annulationForm->createView(),
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("{id}/publier", name="publier")
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function publierDirect(Sortie                 $sortie,
                                  EntityManagerInterface $entityManager,
                                  EtatRepository         $etatRepository): Response
    {
        $etatOuverte = $etatRepository->find(2);
        $sortie->setEtat($etatOuverte);
        $entityManager->flush();
        $this->addFlash('success', 'La sortie a bien été publiée.');
        return $this->redirectToRoute('accueil');

    }

    /**
     * @Route("/lieux-villes", name="lieux-et-villes")
     */
    public function lieuxVilles(LieuRepository $repoLieu, VilleRepository $repoVille): Response
    {
        $tabVille = [];
        foreach ($repoVille->findAll() as $v) {
            $ville['id'] = $v->getId();
            $ville['nom'] = $v->getNom();
            $ville['code_postal'] = $v->getCodePostal();
            $tabVille[] = $ville;
        }

        $tabLieu = [];
        foreach ($repoLieu->findAll() as $l) {
            $lieu['id'] = $l->getId();
            $lieu['nom'] = $l->getNom();
            $lieu['rue'] = $l->getRue();
            $lieu['latitude'] = $l->getLatitude();
            $lieu['longitude'] = $l->getLongitude();
            $lieu['ville']['id'] = $l->getVille()->getId();
            $lieu['ville']['nom'] = $l->getVille()->getNom();

            $tabLieu[] = $lieu;
        }

        $tab['villes'] = $tabVille;
        $tab['lieux'] = $tabLieu;

        return $this->json($tab);
    }
}