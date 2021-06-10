<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Activite;
use App\Entity\DocPdf;
use App\Form\ActiviteType;
use App\Form\SearchForm;
use App\Repository\ActiviteRepository;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class EspaceController extends AbstractController
{
    /**
     * @Route("/trombi", name="trombi")
     * @param UserRepository $userRepository
     * @return Response
     *
     *
     * Cette methode est en charge d'afficher le trombinoscope
     *
     */
    public function index(UserRepository $userRepository): Response
    {
        //On as accès a cette page a partir du moment qu'on est Adhérent
        $this->denyAccessUnlessGranted("ROLE_USER");

        //On redirige l'utilisateur sur la page espace/index.html.twig.
        return $this->render('espace/index.html.twig', [
            'users' => $userRepository->orderUserByReferentWithPhoto(),
        ]);
    }

}
