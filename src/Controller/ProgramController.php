<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Activite;

class ProgramController extends AbstractController
{ #ce controlleur gere la page des programmes#
    /**
     * @Route("/program", name="program")
     */
    public function programIndex(): Response
    {
        #affiche la liste complète des sorties#
        $activiteRepo = $this->getDoctrine()->getRepository(Activite::class);
        $activite=$activiteRepo->findAll();

        return $this->render('program/programList.twig', [
            "activite"=>$activite,
            'controller_name' => 'ProgramController',
        ]);
    }
}
