<?php

namespace App\Controller;

use App\Repository\CarouselImageRepository;
use App\Repository\CarouselRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PublicController extends AbstractController
{
    /**
     * Affichage de la page d'accueil du site
     * @Route("/", name="homepage")
     */
    public function index(CarouselRepository $repo)
    {
        // $carousel = $repo->findOneBySlug('homepage');
        $carousel = $repo->getIfVisible('homepage');
        return $this->render('public/homepage.html.twig', [
            'carousel' => $carousel
        ]);
    }

    /**
     * Accès à l'admin !!! TODO à déplacer dans le futud contoller ADMIN !!!
     * @Route("/admin", name="admin_homepage")
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin()
    {
        return $this->render('admin/homepage.html.twig', []);
    }

    /**
     * @Route("/progiciel-belair", name="belair")
     */
    public function belair()
    {
        return $this->render('public/belair.html.twig', []);
    }

    /**
     * @Route("/logiciels-webservice", name="webservices")
     */
    public function webservices()
    {
        return $this->render('public/webservices.html.twig', []);
    }
}
