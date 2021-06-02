<?php


namespace App\Controller;


use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActualiteController extends AbstractController
{
    /**
     * @Route("/actualite", name="actualite")
     */
    public function actualite(ActualiteRepository $actualiteRepository)
    {
        $products = $actualiteRepository->findAll();
        return$this->render('actualite/gestion_actu.html.twig',[
            'actualites'=>$products
        ]);
    }


    /**
     * @Route("/actu_new", name="actu_new")
     */
    public function new(Request $request, ActualiteRepository $actualiteRepository, EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted ("ROLE_ADMIN");

        $actualite = new Actualite();

        $form = $this->createForm(ActualiteType::class,$actualite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actualite);
            $entityManager->flush();
            $this->addFlash('succes','Une nouvelle actu est créée');

            return $this->redirectToRoute('home1');
        }
        return $this->render('actualite/actu.html.twig',[
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actuedit/{id}", name="actuedit")
     */
    public function editactu(Request $request, Actualite $actualite):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('succes',"l'actualité à été modifiée !!");
            return $this->redirectToRoute('home1');
        }
        return $this->render('actualite/actu.html.twig',[
           'actualite' => $actualite,
           'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actudelete/{id}", name="actudelete")
     */
    public function deleteActualite(Actualite $actualite)
    {
     $this->denyAccessUnlessGranted('ROLE_ADMIN');

     $em=$this->getDoctrine()->getManager();
     $em->remove($actualite);
     $em->flush();
     $this->addFlash('succes',"L'actualité à été supprimer");
     return $this->redirectToRoute('home1');
    }

}