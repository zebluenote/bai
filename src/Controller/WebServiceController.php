<?php

namespace App\Controller;


use Exception;
use LogicException;
use Belair\BasParams;

use Belair\BasAuthClient;
use App\Entity\WebService;
use App\Repository\WebServiceRepository;
use App\Service\BelairApplicationServerHelper;
use Belair\Bas;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class WebServiceController extends AbstractController
{
    /**
     * Affiche la liste de tous les webservices définis dans la base de données
     * 
     * @Route("/admin-web-services-list", name="admin_web_services_index")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param WebServiceRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function index(WebServiceRepository $repo)
    {
        $result = $repo->findAll();
        return $this->render('admin/webservice/index.html.twig', [
            'controller_name' => 'WebServiceController',
            'webservices' => $result
        ]);
    }

    /**
     * @Route("/admin-web-service-show/{id}", name="admin_web_service_show")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param WebService $webservice 
     * @param WebServiceRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function show(WebService $webservice, WebServiceRepository $repo)
    {

        // Récupération des informations en base de données
        $ws = $repo->find($webservice);

        $params = [
            'BAS_DATETIME_FMT' => $this->getParameter('BAS_DATETIME_FMT'),
            'BAS_NS_URI' => $this->getParameter('BAS_NS_URI')
        ];

        $invoices = null;
        $sysInfo = null;

        try {

            $basHelper = new BelairApplicationServerHelper($ws, $params);

            $sysInfo = $basHelper->getSysInfo($ws);

            $invoices = $basHelper->getInvoices(1340);

            $flashMessage = "Il faut gérer la récupération des paramètres services.yaml dans le BelairApplicationServerHelper";
            $this->addFlash('warning', $flashMessage);

            return $this->render('admin/webservice/show.html.twig', [
                'invoices' => $invoices,
                'ws' => $ws,
                'sysinfo' => $sysInfo,
                'datetime' => new \DateTime()
            ]);
    
        } catch (Exception $e) {

            $flashMessage = $e->getMessage();
            $this->addFlash('danger', $flashMessage);

            return $this->redirectToRoute("admin_web_services_index");

        }

    }

    /**
     * @Route("/admin-webservice-status-toggle/{id}", name="admin_webservice_status_toggle", methods={"POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param WebService $webservice 
     * @param EntityManagerInterface $manager 
     * @return RedirectResponse|void 
     */
    public function toggleStatus(WebService $webservice, EntityManagerInterface $manager)
    {

        try {
            $currentStatus = $webservice->getIsActive();
            $visible = "";
            switch($currentStatus) {

                case true:
                case 1:
                    $webservice->setIsActive(false);
                    $visible = "désactivé";
                break;
                
                case false:
                    case 0:
                        $webservice->setIsActive(true);
                        $visible = "activé";
                    break;
                default :
                    $webservice->setIsActive(false);
                    $visible = "désactivé";
            }
            $manager->persist($webservice);
            $manager->flush();
            return $this->json(['code' => 200, 'data' => ['newStatus' => $webservice->getIsActive()], 'message' => 'Ce webservice est désormais [' . $visible . ']'], 200);
        } catch (Exception $e) {
            return $this->json(['code' => 500, 'data' => ['newStatus' => $webservice->getIsActive()], 'message' => $e->getMessage()], 200);
        }
    }
}
