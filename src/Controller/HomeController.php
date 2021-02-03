<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Repository\ActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route ("/", name="hometotal")
     */
    public function redirige(): Response
    {
        return $this->redirectToRoute('home1');
    }



    #ce controlleur gere les pages fixes du site
    /**
     * @Route("/home", name="home1")
     */
    public function index(ActiviteRepository $activiteRepository): Response
    {
        $date=new \DateTime('now');
        $actidispo=$activiteRepository->affichepastille();
        dump($actidispo);$actidispo;

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'actidispo'=>$actidispo,
            'date'=>$date
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
     */
    public function organisation(): Response
    {

        return $this->render('Association/Organisation.html.twig');
    }


    /**
     * @Route ("/randosvelo", name="randosvelo")
     */
    public function randosvelo() : Response
    {
        return $this->render('activite/randos-velo.html.twig');
    }


    /**
     * @Route ("/formations", name="formations")
     */
    public function formations() : Response
    {
        return $this->render('activite/formations.html.twig');
    }


    /**
     * @Route ("/projections", name="projections")
     */
    public function projections() : Response
    {
        return $this->render('activite/projectionsfilms.html.twig');
    }


    /**
     * @Route ("/ecocitoyennete", name="ecocitoyennete")
     */
    public function ecocitoyennete() : Response
    {
        return $this->render('activite/ecocitoyennete.html.twig');
    }


    /**
     * @Route ("/pleinair", name="pleinair")
     */
    public function pleinair() : Response
    {
        return $this->render('activite/pleinair.html.twig');
    }


   /**
    * @Route ("/quisommesnous", name="quisommesnous")
    */
    public function quisommesnous() :Response
    {

        return $this->render('pagesfooter/Qui-sommes-nous.html.twig',[

        ]);
    }


    /**
     * @Route ("/mentionslegales", name="mentionslegales")
     */
    public function mentionslegales() :Response
    {
        return $this->render('pagesfooter/mentions-legales.html.twig');
    }

}
