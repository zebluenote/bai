<?php

namespace App\Controller;

use Belair\BasAuthClient;


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
        // {"nom":"ESPCLI","ip":"bai-pdc-sec","port":"8081","base":"0","user":"SOAPINTRANET","pwd":"K7!@udioHD120","useSessionSC":"1"}
        $authClient = new BasAuthClient( $this->getParameter('BAS_URL'), array("trace" => true, "exceptions" => true));
        $sc = $authClient->OpenSession("SOAPINTRANET", "K7!@udioHD120");
        dump($sc);
        $sysInfo = $authClient->GetSysInfo();
        dump($sysInfo);

        return $this->render('admin/homepage.html.twig', [
            'BAS_DATETIME_FMT' => $this->getParameter('BAS_DATETIME_FMT'),
            'BAS_NS_URI' => $this->getParameter('BAS_NS_URI'),
            'BAS_ENVELOPE_NS' => $this->getParameter('BAS_ENVELOPE_NS'),
            'BAS_TYPE_NS' => $this->getParameter('BAS_TYPE_NS')
        ]);
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
