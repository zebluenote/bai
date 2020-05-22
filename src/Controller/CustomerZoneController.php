<?php

namespace App\Controller;

use LogicException;

use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerZoneController extends AbstractController
{
    /**
     * Affichage de l'accueil de l'espace client
     * 
     * @Route("/espaceclient-accueil", name="espaceclient_accueil")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @param CustomerRepository $customerRepo 
     * @return Response 
     * @throws LogicException 
     */
    public function index(CustomerRepository $customerRepo)
    {
        $user = $this->getUser();
        $companyId = $user->getCustomer()->getId();
        $customer =$customerRepo->findOneBy(['id' => $companyId]);
        
        return $this->render('customerZone/index.html.twig', [
            'user' => $user,
            'customer' => $customer
        ]);

    }

    /**
     * Affichage des release notes du progiciel Belair
     * 
     * @Route("espaceclient-releasenotes-belair", name="espaceclient_releasenotes_belair")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response 
     * @throws LogicException 
     */
    public function releaseNoteBelair()
    {
        return $this->render('customerZone/releaseNoteBelair.html.twig');
    }
}