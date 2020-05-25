<?php

namespace App\Controller\Admin;

use Exception;
use LogicException;
use App\Entity\Carousel;
use App\Form\CarouselType;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CarouselController extends AbstractController
{

    /**
     * Affichage de la liste des carousels
     * 
     * @Route("/admin-carousel-index", name="admin_carousel_index")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param CarouselRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function index(CarouselRepository $repo)
    {
        $carousels = $repo->findAll(true);
        return $this->render('admin/carousel/index.html.twig', [
            'carousels' => $carousels
        ]);
        
    }

    /**
     * Permet d'afficher le détail un carousel
     * 
     * @Route("admin-carousel-show/{id}", name="admin_carousel_show")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Carousel $carousel 
     * @param CarouselRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function show(Carousel $carousel, CarouselRepository $repo)
    {
        $carousel = $repo->find($carousel);
        return $this->render('admin/carousel/show.html.twig', [
            'carousel' => $carousel
        ]);
    }

    /**
     * Permet de modifier un carousel
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @Route("admin-carousel-edit/{id}", name="admin_carousel_edit")
     * 
     * @param Carousel $carousel 
     * @param Request $request 
     * @param EntityManagerInterface $manager 
     * @return Response 
     * @throws LogicException 
     */
    public function edit(Carousel $carousel, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(CarouselType::class, $carousel);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            try {
                // dd($carousel);
                // Gestion des images (carouselElement) en relation avec ce carousel
                // dd($carousel->getCarouselElements());
                foreach( $carousel->getCarouselElements() as $carouselElement ) {
                    $carouselElement->setCarousel($carousel);
                    $manager->persist($carouselElement);
                }
                // dd($carousel->getCarouselElements());                
                $manager->persist($carousel);
                $manager->flush();

                $this->addFlash('success', "Le carousel {$carousel->getName()} a été modifié avec succès.");
                return $this->redirectToRoute('admin_carousel_index');
            } catch (Exception $e) {
                $this->addFlash('danger', $e->getMessage());
                return $this->redirectToRoute('admin_carousel_index');
            }
        }

        return $this->render('admin/carousel/edit.html.twig', [
            'form' => $form->createView(),
            'carousel' => $carousel
        ]);

    }

    /**
     * Suppression d'un carousel
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @Route("admin-carousel-delete/{id}", name="admin_carousel_delete")
     * 
     * @param Carousel $carousel 
     * @param Request $request 
     * @param EntityManagerInterface $manager 
     * @return RedirectResponse|void 
     */
    public function delete(Carousel $carousel, Request $request, EntityManagerInterface $manager)
    {
        try {
            $name = $carousel->getName();
            $manager->remove($carousel);
            $manager->flush();
            $this->addFlash('success', "Le carousel [{$name}] a été supprimé avec succès");
            return $this->redirectToRoute('admin_carousel_index');
        } catch(Exception $e) {
            $this->addFlash('danger', "Une anomalie a empêché la suppression de ce carousel.");
            return $this->redirectToRoute('admin_carousel_index');
        }
    }



    /**
     * Permet de modifier le status d'un carousel en ajax
     * 
     * @Route("admin-carousel-status-toggle/{id}", name="admin_carousel_status_toggle", methods={"POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Carousel $carousel 
     * @param EntityManagerInterface $manager 
     * @return JsonResponse 
     */
    public function toggleStatus(Carousel $carousel, EntityManagerInterface $manager)
    {
        try {
            $currentStatus = $carousel->getStatus();
            $visible = "";
            switch($currentStatus) {

                case true:
                case 1:
                    $carousel->setStatus(false);
                    $visible = "non visible";
                break;
                
                case false:
                    case 0:
                        $carousel->setStatus(true);
                        $visible = "visible";
                    break;
                default :
                    $carousel->setStatus(null);
                    $visible = "non visible";
            }
            $manager->persist($carousel);
            $manager->flush();
            return $this->json(['code' => 200, 'data' => ['newStatus' => $carousel->getStatus()], 'message' => 'Ce carousel est désormais [' . $visible . ']'], 200);
        } catch (Exception $e) {
            return $this->json(['code' => 500, 'data' => ['newStatus' => $carousel->getStatus()], 'message' => $e->getMessage()], 200);
        }
    }

}
