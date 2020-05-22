<?php

namespace App\Controller\Admin;

use LogicException;
use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageController extends AbstractController
{
    /**
     * Liste des images du site
     * 
     * @Route("/admin-image-index", name="admin_image_index")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @return Response 
     * @throws LogicException 
     */
    public function index(ImageRepository $repo)
    {
        $result = $repo->findAll();
        
        $images = [];
        $destinations = [];
        foreach ($result as $image) {
            $destination = $image->getDestination();
            if ( !isset($images[$destination]) ) {
                $images[$destination] = [];
                $destinations[] = $destination;
            }
            $images[$destination][$image->getId()] = $image->getImageFile();
        }

        return $this->render('admin/image/index.html.twig', [
            'destinations' => $destinations,
            'images' => $images
        ]);
    }

    /**
     * Chargement d'une nouvelle image
     * 
     * @Route("admin-image-upload", name="admin_image_upload")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Request $request 
     * @param SluggerInterface $slugger 
     * @param EntityManagerInterface $manager 
     * @return Response 
     * @throws LogicException 
     * @throws ServiceNotFoundException 
     */
    public function upload(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image, [
            'destinations' => explode(',', $this->getParameter('image_destinations'))
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadedFile')->getData();
            $destination = $form->get('destination')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $destinationFullPath = $this->getParameter('images_directory_path').'/'.$destination;
                    if ( !is_dir($destinationFullPath) ) {
                        mkdir( $destinationFullPath );
                    }
                    $uploadedFile->move(
                        $destinationFullPath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', $e->getMessage());
                    return $this->redirectToRoute('admin_image_index');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $image->setImageFile($newFilename);
                $image->setDestination($destination);
                $manager->persist($image);
                $manager->flush();
                $this->addFlash('success', "L'image [<em>{$newFilename}</em>] a été chargée avec succès dans le répertoire [<em>{$destination}</em>]");
                return $this->redirectToRoute('admin_image_index');
            }

        }

        $destinations = explode(',', $this->getParameter('image_destinations'));
        return $this->render('admin/image/upload.html.twig', [
            'destinations' => $destinations,
            'form' => $form->createView()
        ]);

    }
}
