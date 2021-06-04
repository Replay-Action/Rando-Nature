<?php


namespace App\Controller;


use App\Entity\Referent;
use App\Form\ReferentType;
use App\Repository\ReferentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReferentController extends AbstractController
{

    /**
     * @Route ("/referent", name="referent")
     * @param ReferentRepository $referentRepository
     * @return Response
     *
     * Cette méthode sert a rediriger l'utilisateur sur la page Gestion referent,
     * et affiche les tous les referent.
     *
     */
    public function referent(ReferentRepository $referentRepository):Response
    {
        //On récupère les toutes les données de la table referent avec la méthode
        //On envoie les données  sur la page referent.html.twig.
        return $this->render('referent/referent.html.twig', [
             'referents' => $referentRepository->findAll(),
        ]);
    }

    /**
     * @Route ("/createRef", name="createRef")
     * @param Request $request
     * @return Response
     *
     * Cette méthode sert a créer un referent
     *
     */
    public function createReferent(Request $request): Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //On créer une nouvelle instance de l'objet Referent et on le stock dans la variable $referent.
        $referent = new Referent();
        //On créer notre formulaire.
        $form = $this->createForm(ReferentType::class,$referent);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if($form->isSubmitted() && $form->isValid()){

            //On envoie les informations a la base de donnée.
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($referent);
            $entityManager->flush();
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('succes', 'Un nouveau référent à été crée');
            //On redirige l'utilisateur sur la page referent.html.twig
            return $this->redirectToRoute('referent');

        }
            //On envoie les données sur la page newref.html.twig.
            return $this->render('referent/newref.html.twig',[
                'referent' => $referent,
                'form'=> $form->createView(),
            ]);
    }

    /**
     * @Route ("/updateRef/{id}", name="updateRef")
     * @param Request $request
     * @param Referent $referent
     * @return Response
     *
     * Cette méthode sert a modifier un référent.
     *
     */
    public function updateReferent(Request $request, Referent $referent):Response
    {
        #fonction qui permet de modifier un référent#

        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //On créer notre formulaire.
        $form = $this->createForm(ReferentType::class, $referent);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form-> isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            //On envoie les informations modifiées a la base de donnée.
            $entityManager->flush();
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('succes', 'Le référent à été modifier');
            //On redirige l'utilisateur sur la page referent.html.twig
            return $this->redirectToRoute('referent');
        }
        //On envoie les données sur la page editref.html.twig.
        return $this->render('referent/editref.html.twig', [
            'referent' => $referent,
            'form' =>$form->createView(),
        ]);
    }

    /**
     * @Route ("/deleteRef/{id}", name="deleteRef")
     * @param Referent $referent
     * @return Response
     *
     * Cette méthode sert a supprimer un référent.
     *
     */
    public function deleteReferent(Referent $referent): Response
    {
         #fonction qui supprime un référent#

        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em=$this->getDoctrine()->getManager();
        //On supprime le referent.
        $em->remove($referent);
        $em->flush();
        //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
        $this->addFlash('succes', 'Le référent à été supprimer');
        //On envoie les données sur la page referent.html.twig.
        return $this->redirectToRoute('referent');
    }

}