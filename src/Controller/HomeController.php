<?php

namespace App\Controller;

use App\Form\PdfStatusType;
use App\Repository\ActiviteRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route ("/", name="hometotal")
     */
    public function redirige(): Response
    {
#la page sur laquelle on arrive qd on se deconnecte, elle redirige vers 'home1

        return $this->redirectToRoute('home1');

    }



    #ce controlleur gere les pages fixes du site

    /**
     * @Route("/home", name="home1")
     */
    public function index(ActiviteRepository $activiteRepository): Response
    {
        #pour afficher la pastille clignotante de la page accueil
        $date = new DateTime('now');
        $actidispo = $activiteRepository->affichepastille();


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'actidispo' => $actidispo,
            'date' => $date
        ]);
    }

    /**
     * @Route("/presentation", name="presentation")
     */
    public function pres(): Response
    {

        return $this->render('Association/Presentation.html.twig');
    }

    /**
     * @Route("/organisation", name="organisation")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function organisation(UserRepository $userRepository): Response
    {

        return $this->render('Association/Organisation.html.twig',[
            'users' => $userRepository->orderUserByReferent(),
        ]);
    }


    /**
     * @Route ("/randosvelo", name="randosvelo")
     */
    public function randosvelo(): Response
    {
        return $this->render('activite/randos-velo.html.twig');
    }


    /**
     * @Route ("/formations", name="formations")
     */
    public function formations(): Response
    {
        return $this->render('activite/formations.html.twig');
    }


    /**
     * @Route ("/projections", name="projections")
     */
    public function projections(): Response
    {
        return $this->render('activite/projectionsfilms.html.twig');
    }


    /**
     * @Route ("/ecocitoyennete", name="ecocitoyennete")
     */
    public function ecocitoyennete(): Response
    {
        return $this->render('activite/ecocitoyennete.html.twig');
    }

    /**
     * @Route ("/adherent" , name="adherent")
     */
    public function adherent(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route ("/pleinair", name="pleinair")
     */
    public function pleinair(): Response
    {
        return $this->render('activite/pleinair.html.twig');
    }


    /**
     * @Route ("/quisommesnous", name="quisommesnous")
     */
    public function quisommesnous(): Response
    {

        return $this->render('pagesfooter/Qui-sommes-nous.html.twig', [

        ]);
    }


    /**
     * @Route ("/mentionslegales", name="mentionslegales")
     */
    public function mentionslegales(): Response
    {
        return $this->render('pagesfooter/mentions-legales.html.twig');
    }


    /**
     * @Route("/pdfstatus", name="pdfstatus")
     */
    public function editpdf(Request $request): Response
    {
# cette fonction sert à uploader le pdf des status.
# il aura toujours le meme nom car on lui donne ce nom quand on l'uploade

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->createForm(PdfStatusType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            #on le recupere depuis le formulaire
            $uploadedfile = ($form['field_name']->getData());
            $destination = $this->getParameter('upload_directory');
            $originalFilename = pathinfo($uploadedfile->getClientOriginalName(), PATHINFO_FILENAME);

            #on lui donne toujours le meme nom
            $newFilename = 'Statuts_asso_Rando nature Bruz.' . 'pdf';

            #on l'enregistre dans le dossier upload_directory dans public
            $uploadedfile->move(
                $destination,
                $newFilename
            );
            $this->addFlash('message', 'le pdf a bien été modifié');

            return $this->redirectToRoute('home1');
        }

        return $this->render('pagesfooter/edit_pdfStatus.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/chartestatus", name="chartestatus")
     */
    public function editpdfcharte(Request $request): Response
    {
        # cette fonction sert à uploader le pdf de la charte.
        # il aura toujours le meme nom car on lui donne ce nom quand on l'uploade

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->createForm(PdfStatusType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            #on le recupere depuis le formulaire
            $uploadedfile = ($form['field_name']->getData());
            $destination = $this->getParameter('upload_directory');
            $originalFilename = pathinfo($uploadedfile->getClientOriginalName(), PATHINFO_FILENAME);

            #on lui donne toujours le meme nom
            $newFilename = 'Charte_asso_Rando nature Bruz.' . 'pdf';

            #on l'enregistre dans le dossier upload_directory dans public
            $uploadedfile->move(
                $destination,
                $newFilename
            );
            $this->addFlash('message', 'le pdf a bien été modifié');

            return $this->redirectToRoute('home1');
        }

        return $this->render('pagesfooter/edit_pdfCharte.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/pdf_mentionslegales", name="pdfmentionslegales")
     */
    public function editpdfmentions(Request $request): Response
    {
        # cette fonction sert à uploader le pdf des mentions légales.
        # il aura toujours le meme nom car on lui donne ce nom quand on l'uploade

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $form = $this->createForm(PdfStatusType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            #on le recupere depuis le formulaire
            $uploadedfile = ($form['field_name']->getData());
            $destination = $this->getParameter('upload_directory');
            $originalFilename = pathinfo($uploadedfile->getClientOriginalName(), PATHINFO_FILENAME);

            #on lui donne toujours le meme nom
            $newFilename = 'Mentions_legales.' . 'pdf';

            #on l'enregistre dans le dossier upload_directory dans public
            $uploadedfile->move(
                $destination,
                $newFilename
            );
            $this->addFlash('message', 'le pdf a bien été modifié');

            return $this->redirectToRoute('home1');
        }

        return $this->render('pagesfooter/edit_pdfMentions.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}
