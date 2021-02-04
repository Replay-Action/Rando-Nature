<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Activite;

class ProgramController extends AbstractController
{ #ce controlleur gere la page des programmes#
    /**
     * @Route("/administrateur", name="administrateur")
     */
    public function programIndex(): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");


        return $this->render('program/programList.twig');
    }
}
