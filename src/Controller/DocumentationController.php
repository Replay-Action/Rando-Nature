<?php


namespace App\Controller;


use App\Entity\Commentaire;
use App\Entity\Documentation;
use App\Form\CommentaireType;
use App\Form\DocumentationType;
use App\Repository\CommentaireRepository;
use App\Repository\DocumentationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationController extends AbstractController
{
    /**
     * @Route ("/documentation", name="documentation")
     * @param DocumentationRepository $documentationRepository
     * @return Response
     */
    public function documentation(DocumentationRepository $documentationRepository): Response
    {
        $documentation = $documentationRepository->findAll();

        return $this->render('documentation/documentation.html.twig',[
            'documentation' => $documentation,
        ]);
    }

    /**
     * @Route ("/show/documentation/{id}", name="show_documentation")
     * @param Documentation $documentation
     * @param DocumentationRepository $documentationRepository
     * @param CommentaireRepository $commentaireRepository
     * @param Request $request
     * @return Response
     */
    public function showDocumentation(Documentation $documentation,DocumentationRepository $documentationRepository,
                                      CommentaireRepository $commentaireRepository,Request $request): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $documentations = $documentationRepository->findOneBy(['id' => $documentation]);
        $commentaires = $commentaireRepository->findAll();

        $comment = new Commentaire;
        $entityManager = $this->getDoctrine()->getManager();
        $name = $this->getUser()->getUsername();

        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setUserName($name);
            $comment->setDateCreation(new DateTime('now'));
            $comment->setDocumentation($documentation);

            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('documentation');
        }
        return $this->render('documentation/show_documentation.html.twig',[
            'documentations' => $documentations,
            'commentaires' => $commentaires,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/create/documentation", name="create_documentation")
     * @param Request $request
     * @return Response
     */
    public function createDocumentation(Request $request): Response{

        $this->denyAccessUnlessGranted("ROLE_USER");

        $documentation = new Documentation();
        $user = $this->getUser()->getUsername();

        $formDocumentation = $this->createForm(DocumentationType::class, $documentation);
        $formDocumentation->handleRequest($request);

        if($formDocumentation->isSubmitted() && $formDocumentation->isValid())
        {
            $documentation->setAuteur($user);
            $documentation->setDateCreation(new DateTime('now'));

            if( $documentation->getImage() != null ){
                $file = $documentation->getImage();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('documentation_directory'),$fileName);
                $documentation->setImage($fileName);
                $documentation->setImageModification($fileName);
            }
            if($documentation->getImage2() != null){
                $file = $documentation->getImage2();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('documentation_directory'),$fileName);
                $documentation->setImage2($fileName);
                $documentation->setImageModification2($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($documentation);
            $entityManager->flush();
            return $this->redirectToRoute('documentation');
        }
        return $this->render('documentation/new_documentation.html.twig',[
            'documentation'=> $documentation,
            'form'=> $formDocumentation->createView(),
            ]);
    }

    /**
     * @Route ("/update/documentation/{id}", name="update_documentation")
     * @param Documentation $documentation
     * @param Request $request
     * @return Response
     */
    public function updateDocumentation(Documentation $documentation, Request $request): Response{

        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();

        $image = $documentation->getImage();
        $image2 = $documentation->getImage2();

        $imageModification =  $documentation->getImageModification();
        $imageModification2 = $documentation->getImageModification2();

        $form = $this->createForm(DocumentationType::class, $documentation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $imageActuel = $documentation->getImage();
            $imageActuel2= $documentation->getImage2();

            $documentation->setDateModifier(new DateTime('now'));

                if($imageActuel !== $imageModification && $imageActuel != null )
                {
                    unlink($this->getParameter('documentation_directory') . '/' . $image);

                    $file = $documentation->getImage();
                    $fileName = md5(uniqid()). '.' .$file->guessExtension();
                    $file->move($this->getParameter('documentation_directory'),$fileName);
                    $documentation->setImage($fileName);
                } else {
                    $documentation->setImage($imageModification);
                }

                 if($imageActuel2 !== $imageModification2 && $imageActuel2 != null)
                 {
                     unlink($this->getParameter('documentation_directory') . '/' . $image2);

                     $file = $documentation->getImage2();
                     $fileName = md5(uniqid()). '.' .$file->guessExtension();
                     $file->move($this->getParameter('documentation_directory'),$fileName);
                     $documentation->setImage2($fileName);
                 } else {
                     $documentation->setImage2($imageModification2);
                 }


            $entityManager->persist($documentation);
            $entityManager->flush();

            return $this->redirectToRoute('documentation');
        }
        return $this->render('documentation/update_documentation.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/update/commentaire/{id}", name="update_commentaire")
     * @param Commentaire $commentaire
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateCommentaire(Commentaire $commentaire,Request $request):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $commentaire->setDateModification(new DateTime('now'));

            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('documentaire');
        }
        return $this->render('documentation/update_commentaire.html.twig',[
           'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/delete/documentation/{id}", name="delete_documentation")
     * @param Documentation $documentation
     * @return RedirectResponse
     */
    public function deleteDocumentation(Documentation $documentation): RedirectResponse
    {
        $this->denyAccessUnlessGranted("ROLE_USER");


        $entityManager = $this->getDoctrine()->getManager();
        if( $documentation->getImage() != null) {
            $nom = $documentation->getImage();
            unlink($this->getParameter('documentation_directory') . '/' . $nom);

        }
        $entityManager->remove($documentation);
        $entityManager->flush();

        return $this->redirectToRoute('documentation');
    }

    /**
     * @Route ("/delete/commentaire/{id}", name="delete_commentaire")
     * @param Commentaire $commentaire
     * @return RedirectResponse
     */
    public function deleteCommentaire(Commentaire $commentaire): RedirectResponse
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($commentaire);
        $entityManager->flush();
        return $this->redirectToRoute('documentation');
    }
}