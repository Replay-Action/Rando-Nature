<?php


namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PartenaireController extends AbstractController
{
    /**
     * @Route ("/partenaire", name="partenaire")
     */
    public function partenaire(PartenaireRepository $partenaireRepository, EntityManagerInterface $entityManager): Response
    {



        $products = $partenaireRepository->findPartenaire();




        return $this->render('partenaire/partenaire.html.twig',[

            'partenaires' => $products
        ]);
    }

    /**
     * @Route ("/createP", name="createP")
     */
    public function createPartenaire(Request $request): Response
    {
        $partenaire = new Partenaire();

        $formPartenaire = $this->createForm(PartenaireType::class, $partenaire);
        $formPartenaire->handleRequest($request);

        if($formPartenaire->isSubmitted() && $formPartenaire->isValid()){
            $file = $partenaire->getLogo();
            $fileName = md5(uniqid()). '.' .$file->guessExtension();
            $file->move($this->getParameter('logo_directory'),$fileName);
            $partenaire->setLogo($fileName);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($partenaire);
            $entityManager->flush();
            return $this->redirectToRoute('home1');
        }

        return $this->render('partenaire/_formPartenaire.html.twig',[
            'partenaire' => $partenaire,
            'formPartenaire' => $formPartenaire->createView(),
        ]);

    }
}