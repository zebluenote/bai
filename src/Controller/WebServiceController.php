<?php

namespace App\Controller;

use Exception;
use LogicException;
use Belair\BasParams;

use Belair\BasAuthClient;
use App\Entity\WebService;
use Belair\BasActionClient;
use App\Repository\WebServiceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        // interrogation du web service pour récupération de ses informations système
        $authClient = new BasAuthClient( $this->getParameter('BAS_URL'), array("trace" => true, "exceptions" => true));
        $sc = $authClient->OpenSession($ws->getParams()['user'], $ws->getParams()['pwd']);
        $sysInfo = $authClient->GetSysInfo();

        

// Exercice interrogataion des factures
$action = "Appsec_FactureListItems";

$y = date("Y")-2;
$dmin = new \DateTime($y.'-01-01');

$args = array(
    'param' => array(
        0 => array(
            'name' => 'dossier',
            'type' => 'ptInt',
            'value' => 1340
        )
    )
);

if (isset($dmin)) {
    $args['param'][] = array(
        'name' => 'datemin',
        'type' => 'ptDateTime',
        'value' => $dmin->format($this->getParameter('BAS_DATETIME_FMT'))
    );
}
$actionClient = new BasActionClient($this->getParameter('BAS_URL'), array("trace" => true, "exceptions" => true));

$params = new BasParams();

	foreach ($args['param'] as $param ) {

		$name = $param['name'];
		$value = $param['value'];
		$type = $param['type'];

		switch ( $type ) {
			case 'ptString' :
				$params->AddString($name, $value);
				break;
			case 'ptInt' :
					$params->AddInt($name, $value);
				break;
			case 'ptFloat' :
					$params->AddFloat($name, $value);
				break;
			case 'ptBool' :
					$params->AddBool($name, $value);
				break;
			case 'ptDateTime' :
					$params->AddDateTimeFmt($name, $this->getParameter('BAS_DATETIME_FMT'), $value);
				break;
			default :
				throw new Exception("Data type [".$param['type']."] is unknown, please check your data.", 1);
		}

	}

    $resultAction = $actionClient->RunAction($sc, $action, $params->ToSoapVar("params", $this->getParameter('BAS_NS_URI')));
    // dump($resultAction->Data);
    $result = simplexml_load_string($resultAction->Data);
    $result = json_encode($result);
    dump( json_decode($result, true) );

        return $this->render('admin/webservice/show.html.twig', [
            'ws' => $ws,
            'sysinfo' => $sysInfo,
            'datetime' => new \DateTime()
        ]);
    }
}
