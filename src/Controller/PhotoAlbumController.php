<?php


namespace App\Controller;


use App\Entity\Activite;
use App\Entity\DocPdf;
use App\Entity\PhotoAlbum;
use App\Form\DocPdfType;
use App\Form\PhotoAlbumType;
use App\Repository\ActiviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class PhotoAlbumController extends AbstractController
{
    /**
     * @Route ("/album", name="album")
     * @param ActiviteRepository $activiteRepository
     * @return Response
     *
     * Cette méthode sert a rediriger l'utilisateur sur la page Album photo,
     * et Permet aussi de faire l'affichage des activités avec l'état 'finie' ou 'annulé'.
     *
     */
    public function album(ActiviteRepository $activiteRepository): Response
    {
        //On récupère toutes les données de la table activité avec la méthode findActivité().
        //On envoie les données récupérer sur la page album_photo.html.twig.
        return $this->render('album/album_photo.html.twig',[
            'activite' => $activiteRepository->findActivites(),
        ]);
    }

    /**
     * @Route ("/album/create/{id}", name="create_album")
     * @param Request $request
     * @param Activite $activite
     * @return Response
     *
     * Cette méthode sert a créé une image et a le lier a une activité.
     * La méthode ne dispose pas de redirection pour permettre a l'utilisateur
     * d'insérer plusieurs image sans gène.
     *
     */
    public function createAlbum(Request $request, Activite $activite): Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        //On créer une nouvelle instance de l'objet PhotoAlbum et on le stock dans la variable $album.
        $album = new PhotoAlbum();
        //On créer notre formulaire.
        $form = $this->createForm(PhotoAlbumType::class, $album);
        //On récupère les information saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid())
        {
            //On injecte l'activité dans le le setter activite
            //de cette façon l'activité et l'album son lier par le même id.
            $album->setActivite($activite);
            //Si la propriété image n'est pas null (vide)...
            if( $album->getImage() != null )
            {
                //On stock le nom du fichier dans la variable $file.
                $file = $album->getImage();
                //On renomme le fichier avec un nom unique et on lui ajoute l'extension contenue
                //dans la variable $file, on stock le tout dans la variable $fileName.
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                //On déplace le fichier dans le repository album-photo.
                $file->move($this->getParameter('album_directory'),$fileName);
                //On injecte le nouveau nom du fichier dans la propriété image.
                $album->setImage($fileName);
            }
            //On envoie les informations a la base de donnée.
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($album);
            $entityManager->flush();
            //On renvoie un message de success a l'utilisateur pour prévenir de la réussite.
            $this->addFlash('success', 'Votre image a bien été insérer dans l\'album');
        }
        //On envoie les données sur la page new_album.html.twig
        return $this->render('album/new_album.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/show/album/{id}", name="show_album")
     * @param Activite $activite
     * @param ActiviteRepository $activiteRepository
     * @return Response
     *
     * Cette méthode est en charge de l'affichage d'une activité (plus détaillé),
     * de l'affichage des pdf et des images qui lui sont lié.
     *
     */
    public function showAlbum(Activite $activite, ActiviteRepository $activiteRepository): Response
    {
        //On recherche une activité par son id et on la stock dans la variable $activite grace a la méthode FindOneBy.
        $activite = $activiteRepository->findOneBy(['id'=> $activite]);

        //On envoie les données sur la page show_album.html.twig
        return $this->render('album/show_album.html.twig',[
            'activite' => $activite,
        ]);
    }

    /**
     * @Route ("/delete/album/{id}", name="delete_album")
     * @param PhotoAlbum $photoAlbum
     * @return Response
     *
     * Cette méthode sert a supprimer les photos stocké dans une activité.
     *
     */
    public function deleteAlbum(PhotoAlbum $photoAlbum): Response
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $entityManager = $this->getDoctrine()->getManager();
        //Si la propriété image n'est pas null (vide)...
        if ($photoAlbum->getImage() != null)
        {
            //On récupère le nom de l'image stocké en base de donnée et on le stock dans la variable $nom.
            $nom = $photoAlbum->getImage();
            //On supprime le fichier stocké dans le repository album-photo.
            unlink($this->getParameter('album_directory').'/'.$nom);
        }
        //On supprime le fichier stocker dans la base de donnée
        $entityManager->remove($photoAlbum);
        $entityManager->flush();
        //On renvoie un message de success pour prévenir l'utilisateur de la réussite.
        $this->addFlash('success', 'L\'image a bien été supprimé de l\'album');
        //On redirige l'utilisateur sur la page album.html.twig.
        return $this->redirectToRoute('album');
    }

    /**
     * @Route ("/pdf/create/{id}", name="create_pdf")
     * @param Request $request
     * @param Activite $activite
     * @return Response
     *
     * Cette méthode est en charge de créer un pdf et de lier a une activité.
     *
     */
    public function createPdf(Request $request, Activite $activite): Response
    {
        //On refuse l'accès a cette méthode si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        //On créer une nouvelle instance de l'objet DocPdf et on le stock dans la variable $pdf.
        $pdf = new DocPdf();
        //On créé notre formulaire.
        $form = $this->createForm(DocPdfType::class, $pdf);
        //On récupère les informations saisi.
        $form->handleRequest($request);
        //Si le formulaire a bien été envoyer et qu'il est valide ...
        if ($form->isSubmitted() && $form->isValid())
        {
            //On injecte l'activité dans le le setter pdfactivite
            //de cette façon l'activité et le pdf son lier par le même id.
            $pdf->setPdfactivite($activite);
            //Si la propriété nom de l'objet pdf est diffèrent de null(rien)...
            if ($pdf->getNompdf() != null)
            {
                //on stock le nom du fichier dans la variable $file.
                $file = $form->get('nompdf')->getData();
                //On renomme le fichier avec un nom unique et on lui ajout l'extension contenue dans le $file
                //on stock le tout dans la variable $fileName.
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                //On déplacer le fichier contenue dans $file dans le repository recap
                //on stock le fichier pdf dans ce repository.
                $file->move($this->getParameter('upload_recap_directory'),$fileName);
                //On injecte le nom unique créer précédemment dans l'attribut nom.
                $pdf->setNompdf($fileName);
            }
            //On envoie les informations a la base de donnée.
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pdf);
            $entityManager->flush();
            //On envoie un message de success pour prévenir l'utilisateur de la réussite.
            $this->addFlash('success', 'Votre pdf a bien été créé');
            //On redirige l'utilisateur vers la page album_photo.html.twig.
            return $this->redirectToRoute('album');
        }
        //On envoie l'affichage du formulaire sur la page new_pdf.html.twig.
        return $this->render('album/new_pdf.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/delete/pdf/{id}", name="delete_pdf")
     * @param DocPdf $docPdf
     * @return Response
     *
     * cette méthode est en charge supprimer un pdf.
     *
     */
    public function deletePdf(DocPdf $docPdf):Response
    {
        //On refuse l'accès a cette méthode a l'utilisateur si l'utilisateur n'a pas le rôle Admin.
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $entityManager = $this->getDoctrine()->getManager();
        //Si la propriété image n'est pas null (vide)...
        if ($docPdf->getNompdf() != null)
        {
            //On récupère le nom de l'image stocké en base de donnée.
            $nom = $docPdf->getNompdf();
            //On supprime le fichier stocker dans le repository recap.
            unlink($this->getParameter('upload_recap_directory').'/'.$nom);
        }
        //On supprime les valeur stockées dans la base de donnée.
        $entityManager->remove($docPdf);
        $entityManager->flush();
        //On revoie un message a l'utilisateur pour lui confirmé la suppression du pdf.
        $this->addFlash('success','Le pdf a bien été supprimé');
        //On redirige l'utilisateur sur la page album.html.twig.
        return $this->redirectToRoute('album');
    }
}