<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Activite;

class ProgramController extends AbstractController
{
    /**
     * @Route("/administrateur", name="administrateur")
     */
    public function programIndex(): Response
    {
        #Attention contrairement à son nom, cette fonction affiche la page administrateur

        $this->denyAccessUnlessGranted("ROLE_ADMIN");


        return $this->render('program/programList.twig');
    }
}
