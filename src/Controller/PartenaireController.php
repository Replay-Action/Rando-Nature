<?php


namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PartenaireController extends AbstractController
{
    /**
     * @Route ("/partenaire", name="partenaire")
     * @param PartenaireRepository $partenaireRepository
     * @return Response
     */
    public function partenaire(PartenaireRepository $partenaireRepository): Response
    {
        $products = $partenaireRepository->findPartenaire();

        return $this->render('partenaire/partenaire.html.twig',[

            'partenaires' => $products
        ]);
    }

    /**
     * @Route ("/createP", name="createP")
     * @param Request $request
     * @return Response
     */
    public function createPartenaire(Request $request): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

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

    /**
     * @Route("/delete/{id}", name="partenaire_delete")
     * @param Partenaire $partenaire
     * @return RedirectResponse
     */
    public function partenaireDelete(Partenaire $partenaire): RedirectResponse{
        $this->denyAccessUnlessGranted("ROLE_USER");

        $em = $this->getDoctrine()->getManager();
        $nom = $partenaire->getLogo();
        unlink($this->getParameter('logo_directory') . '/' . $nom);
        $em->remove($partenaire);
        $em->flush();

        return $this->redirectToRoute('partenaire');
    }

    /**
     * @Route ("/update/{id}", name="partenaire_update")
     * @param Partenaire $partenaire
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function partenaireUpdate(Partenaire $partenaire, Request $request): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $partenaire = new Partenaire();
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);


        $file = $partenaire->getLogo();
        $fileName = md5(uniqid()). '.' .$file->guessExtension();
        $file->move($this->getParameter('logo_directory'),$fileName);
        $partenaire->setLogo($fileName);
        if ($form->isSubmitted() && $form->isValid()) {

            $nom = $partenaire->getLogo();
            unlink($this->getParameter('logo_directory') . '/' . $nom);

            $entityManager->remove($nom);
            $entityManager->persist($partenaire);
            $entityManager->flush();

            return $this->redirectToRoute('partenaire');
        }
        return $this->render('partenaire/updatePartenaire.html.twig',[
           'form' => $form->createView()
        ]);
    }
}