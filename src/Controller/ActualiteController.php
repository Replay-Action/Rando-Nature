<?php


namespace App\Controller;


use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActualiteController extends AbstractController
{
    /**
     * @Route("/actualite", name="actualite")
     * @param ActualiteRepository $actualiteRepository
     * @return Response
     *
     *
     * Cette methode est en charge d'afficher l'actualité sur la page Gestion des Actualités
     *
     */
    public function actualite(ActualiteRepository $actualiteRepository)
    {
        //on récupère la fonction afficheactu et on la sauvegarde dans $products.
        $products = $actualiteRepository->afficheactu();
        //on redirige la fonction actualite sur la page gestion_actu.html.twig.
        return$this->render('actualite/gestion_actu.html.twig',[
            'actualites'=>$products
        ]);
    }


    /**
     * @Route("/actu_new", name="actu_new")
     * @param Request $request
     * @param ActualiteRepository $actualiteRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     *
     * Cette methode est en charge de créer une nouvelle Actualite
     *
     */
    public function new(Request $request, ActualiteRepository $actualiteRepository, EntityManagerInterface $entityManager):Response
    {
        //On laisse l'accès à cette fonction seulement aux Administrateur.
        $this->denyAccessUnlessGranted ("ROLE_ADMIN");
        //On crée une nouvelle Actualite.
        $actualite = new Actualite();
        //On récupère le formulaire dans ActualiteType.
        $form = $this->createForm(ActualiteType::class,$actualite);

        $form->handleRequest($request);

        //Si le formulaire a été envoyer et est valide ...
        if ($form->isSubmitted() && $form->isValid()){
            //On envoie les informations à la base de donnée.
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actualite);
            $entityManager->flush();
            //On renvoie un message de succes à l'utilisateur pour prévenir de la réussite de la création.
            $this->addFlash('succes','Une nouvelle actualité à été créée');
            //On redirige l'utilisateur sur la page home/index.html.twig
            return $this->redirectToRoute('home1');
        }
        //On envoie les données et l'affichage du formulaire sur la page actu.html.twig.
        return $this->render('actualite/actu.html.twig',[
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actuedit/{id}", name="actuedit")
     * @param Request $request
     * @param Actualite $actualite
     * @return Response
     *
     *
     * Cette methode est en charge de modifier une Actualite
     *
     */
    public function editactu(Request $request, Actualite $actualite):Response
    {
        //On laisse l'accès à cette fonction seulement aux Administrateur.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //On récupère le formulaire dans ActualiteType.
        $form = $this->createForm(ActualiteType::class, $actualite);

        $form->handleRequest($request);
        //Si le formulaire a été envoyer et est valide ...
        if ($form->isSubmitted()&& $form->isValid())
        {
            //On envoie les informations de modification à la base de donnée.
            $this->getDoctrine()->getManager()->flush();
            //On renvoie un message de succes à l'utilisateur pour prévenir de la réussite de la modification.
            $this->addFlash('succes',"l'actualité à été modifiée !!");
            //On redirige l'utilisateur sur la page home/index.html.twig
            return $this->redirectToRoute('home1');
        }
        //On envoie les données et l'affichage du formulaire sur la page actu.html.twig.
        return $this->render('actualite/editactu.html.twig',[
           'actualite' => $actualite,
           'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actudelete/{id}", name="actudelete")
     * @param Actualite $actualite
     * @return Response
     *
     *
     * Cette methode est en charge de supprimer une Actualite
     *
     */

    public function deleteActualite(Actualite $actualite):Response
    {
     //On laisse l'accès à cette fonction seulement aux Administrateur.
     $this->denyAccessUnlessGranted('ROLE_ADMIN');
    //On supprime les informations de la base de donnée.
     $em=$this->getDoctrine()->getManager();
     $em->remove($actualite);
     $em->flush();
     //On renvoie un message de succes à l'utilisateur pour prévenir de la réussite de la suppresion.
     $this->addFlash('succes',"L'actualité à été supprimer");
     //On redirige l'utilisateur sur la page home.html.twig.
     return $this->redirectToRoute('home1');
    }

}