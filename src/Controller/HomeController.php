<?php

namespace App\Controller;

use App\Form\PdfStatusType;
use App\Repository\ActiviteRepository;
use App\Repository\ActualiteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route ("/", name="hometotal")
     *
     * Cette méthode est en charge de redirige l'utilisateur sur la page accueil
     * lors d'une déconnexion.
     *
     */
    public function redirige(): Response
    {
        // Redirige vers la page index.html.twig.
        return $this->redirectToRoute('home1');
    }



    #ce controlleur gere les pages fixes du site

    /**
     * @Route("/home", name="home1")
     * @param ActiviteRepository $activiteRepository
     * @return Response
     *
     * Cette méthode est en charge de rediriger l'utilisateur vers la page accueil.
     *
     */
    public function index(ActiviteRepository $activiteRepository, ActualiteRepository $actualiteRepository): Response
    {
        #pour afficher la pastille clignotante de la page accueil
        /**$date = new DateTime('now');*/

        //Permet d'afficher le nombre d'activité en cours
        //et de rediriger sur la page index.html.twig (accueil)
        return $this->render('home/index.html.twig', [
            'actidispo' => $activiteRepository->affichepastille(),
            'actualites' => $actualiteRepository->afficheactu(),
            /**
            'controller_name' => 'HomeController',
            'date' => $date
             * */
        ]);
    }

    /**
     * @Route("/presentation", name="presentation")
     *
     * Cette méthode est en charge de rediriger vers la page Association.
     *
     */
    public function pres(): Response
    {
        //Permet de rediriger vers la page présentation.html.twig.
        return $this->render('Association/Presentation.html.twig');
    }

    /**
     * @Route("/organisation", name="organisation")
     * @param UserRepository $userRepository
     * @return Response
     *
     * Cette méthode est en charge de rediriger vers la page Organisation bureau.
     *
     */
    public function organisation(UserRepository $userRepository): Response
    {
        //Permet de rediriger vers la page organisation.html.twig (organisation bureau)
        // et permet de gérer l'affichage des adhérent par leur référencement.
        return $this->render('Association/Organisation.html.twig',[
            'users' => $userRepository->orderUserByReferent(),
        ]);
    }


    /**
     * @Route ("/randosvelo", name="randosvelo")
     *
     * Cette méthode est en charge de rediriger vers la page Rando Vélos.
     *
     */
    public function randosvelo(): Response
    {
        //Permet de rediriger vers la page rando-velo.html.twig. ( Rando vélos)
        return $this->render('activite/randos-velo.html.twig');
    }


    /**
     * @Route ("/formations", name="formations")
     *
     * Cette méthode est en charge de rediriger vers la page Formations.
     *
     */
    public function formations(): Response
    {
        //Permet de rediriger vers la page formations.html.twig ( Formation )
        return $this->render('activite/formations.html.twig');
    }


    /**
     * @Route ("/projections", name="projections")
     *
     * Cette méthode est en charge de rediriger vers la page Projections.
     *
     */
    public function projections(): Response
    {
        //Permet de rediriger vers la page projectionsfilms.html.twig ( Projection )
        return $this->render('activite/projectionsfilms.html.twig');
    }


    /**
     * @Route ("/ecocitoyennete", name="ecocitoyennete")
     *
     * Cette méthode est en charge de rediriger vers la page Écocitoyenneté.
     *
     */
    public function ecocitoyennete(): Response
    {
        //Permet de rediriger vers la page ecocitoyennete.html.twig ( Écocitoyenneté )
        return $this->render('activite/ecocitoyennete.html.twig');
    }

    /**
     * @Route ("/pleinair", name="pleinair")
     *
     * Cette méthode est en charge de rediriger vers la page Autres activités de pleine air..
     *
     */
    public function pleinair(): Response
    {
        //Permet de rediriger vers la page pleinair.html.twig ( Autres activité de pleine air )
        return $this->render('activite/pleinair.html.twig');
    }

    /**
     * @Route ("/adherent" , name="adherent")
     *
     * Cette méthode est en charge de rediriger vers la page Gestion des adhérents.
     *
     */
    public function adherent(): Response
    {
        //Permet de rediriger vers la page index.html.twig ( gestion adhérent )
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route ("/quisommesnous", name="quisommesnous")
     *
     * Cette méthode est en charge de rediriger vers la page Qui sommes nous ?.
     *
     */
    public function quisommesnous(): Response
    {
        //Permet de rediriger vers la page Qui-sommes-nous.html.twig( Qui somme nous ?)
        return $this->render('pagesfooter/Qui-sommes-nous.html.twig', [

        ]);
    }


    /**
     * @Route ("/mentionslegales", name="mentionslegales")
     *
     * Cette méthode est en charge de rediriger vers la page Mentions légales.
     *
     */
    public function mentionslegales(): Response
    {
        //Permet de rediriger vers la page mentions-legales.html.twig( Mention légales )
        return $this->render('pagesfooter/mentions-legales.html.twig');
    }


    /**
     * @Route("/pdfstatus", name="pdfstatus")
     * @param Request $request
     * @return Response
     *
     * Cette méthode est en charge de créer un pdf.
     *
     */
    public function editpdf(Request $request): Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //On créer notre formulaire.
        $form = $this->createForm(PdfStatusType::class);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid()) {

            //on le recupere depuis le formulaire.
            $uploadedfile = ($form['field_name']->getData());
            $destination = $this->getParameter('upload_directory');
            $originalFilename = pathinfo($uploadedfile->getClientOriginalName(), PATHINFO_FILENAME);

            //on lui donne toujours le meme nom.
            $newFilename = 'Statuts_asso_Rando nature Bruz.' . 'pdf';

            //on l'enregistre dans le dossier upload_directory dans public.
            $uploadedfile->move(
                $destination,
                $newFilename
            );
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('message', 'le pdf a bien été modifié');
            //On redirige l'utilisateur sur la page index.html.twig (accueil).
            return $this->redirectToRoute('home1');
        }
        //On envoie les données sur la page edit_pdfStatus.html.twig.
        return $this->render('pagesfooter/edit_pdfStatus.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/chartestatus", name="chartestatus")
     * @param Request $request
     * @return Response
     *
     * Cette méthode est en charge de créer un pdf.
     *
     */
    public function editpdfcharte(Request $request): Response
    {
        # cette fonction sert à uploader le pdf de la charte.
        # il aura toujours le meme nom car on lui donne ce nom quand on l'uploade

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->createForm(PdfStatusType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on le recupere depuis le formulaire.
            $uploadedfile = ($form['field_name']->getData());
            $destination = $this->getParameter('upload_directory');
            $originalFilename = pathinfo($uploadedfile->getClientOriginalName(), PATHINFO_FILENAME);
            //on lui donne toujours le meme nom.
            $newFilename = 'Charte_asso_Rando nature Bruz.' . 'pdf';

            //on l'enregistre dans le dossier upload_directory dans public.
            $uploadedfile->move(
                $destination,
                $newFilename
            );
            //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
            $this->addFlash('message', 'le pdf a bien été modifié');
            //On redirige l'utilisateur sur la page index.html.twig (accueil).
            return $this->redirectToRoute('home1');
        }
        //On envoie les données sur la page edit_pdfCharte.html.twig.
        return $this->render('pagesfooter/edit_pdfCharte.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/pdf_mentionslegales", name="pdfmentionslegales")
     * @param Request $request
     * @return Response
     *
     * Cette méthode est en charge de créer un pdf.
     *
     */
    public function editpdfmentions(Request $request): Response
    {
        # cette fonction sert à uploader le pdf des mentions légales.
        # il aura toujours le meme nom car on lui donne ce nom quand on l'uploade

        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //On créer notre formulaire.
        $form = $this->createForm(PdfStatusType::class);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid()) {
            //on le recupere depuis le formulaire.
            $uploadedfile = ($form['field_name']->getData());
            $destination = $this->getParameter('upload_directory');
            $originalFilename = pathinfo($uploadedfile->getClientOriginalName(), PATHINFO_FILENAME);
            //on lui donne toujours le meme nom
            $newFilename = 'Mentions_legales.' . 'pdf';

            //on l'enregistre dans le dossier upload_directory dans public.
            $uploadedfile->move(
                $destination,
                $newFilename
            );
            //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
            $this->addFlash('message', 'le pdf a bien été modifié');
            //On redirige l'utilisateur sur la page index.html.twig (accueil).
            return $this->redirectToRoute('home1');
        }
        //On envoie les données sur la page edit_pdfMentions.html.twig.
        return $this->render('pagesfooter/edit_pdfMentions.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
