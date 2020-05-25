<?php
// src/Service/BelairApplicationServerHelper.php
namespace App\Service;

use Exception;
use Belair\BasParams;
use Belair\BasAuthClient;
use Belair\BasActionClient;

class BelairApplicationServerHelper
{

    protected $url;
    protected $authClient;
    protected $session;
    protected $parameters;

    public function __construct($ws, $params)
    {
        // http://192.192.192.71:8081
        $this->url = $ws->getParams()['protocol'] . '://' . $ws->getParams()['ip'] . ':' . $ws->getParams()['port'];
        $this->authClient = $this->authClient($ws);
        $this->session = $this->openSession($ws, $this->authClient);
        $this->parameters = [];
        foreach ($params as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    /**
     * 
     * @param mixed $ws 
     * @return BasAuthClient 
     */
    private function authClient($ws)
    {
        return new BasAuthClient($this->url, array("trace" => true, "exceptions" => true));
    }

    /**
     * Ouverture d'une session BAS sur le WebService fourni
     * 
     * @param mixed $ws 
     * @return mixed 
     */
    private function openSession($ws, $authClient)
    {
        return $authClient->OpenSession($ws->getParams()['user'], $ws->getParams()['pwd']);
    }

    public function getSysInfo()
    {
        return $this->authClient->GetSysInfo();
    }

    public function getInvoices($customerId)
    {

        // Exercice interrogataion des factures
        $action = "Appsec_FactureListItems";

        $y = date("Y") - 2;
        $dmin = new \DateTime($y . '-01-01');

        $args = [
            'param' => [
                0 => [
                    'name' => 'dossier',
                    'type' => 'ptInt',
                    'value' => $customerId
                ]
            ]
        ];

        if (isset($dmin)) {
            $args['param'][] = array(
                'name' => 'datemin',
                'type' => 'ptDateTime',
                'value' => $dmin->format($this->parameters['BAS_DATETIME_FMT'])
            );
        }

        $actionClient = new BasActionClient($this->url, array("trace" => true, "exceptions" => true));

        $params = $this->setArguments($args);

        $resultAction = $actionClient->RunAction($this->session, $action, $params->ToSoapVar("params", $this->parameters['BAS_NS_URI']));
        $tmp = simplexml_load_string($resultAction->Data);
        $tmp = json_encode($tmp);
        $tmp = json_decode($tmp, true, 512, JSON_OBJECT_AS_ARRAY);
        if (isset($tmp['objects']['object'])) {
            return $this->parseEntities($tmp['objects']['object']);
        } else {
            return [];
        }
    }

    private function setArguments($args)
    {
        $params = new BasParams();

        foreach ($args['param'] as $param) {

            $name = $param['name'];
            $value = $param['value'];
            $type = $param['type'];

            switch ($type) {
                case 'ptString':
                    $params->AddString($name, $value);
                    break;
                case 'ptInt':
                    $params->AddInt($name, $value);
                    break;
                case 'ptFloat':
                    $params->AddFloat($name, $value);
                    break;
                case 'ptBool':
                    $params->AddBool($name, $value);
                    break;
                case 'ptDateTime':
                    $params->AddDateTimeFmt($name, $this->parameters['BAS_DATETIME_FMT'], $value);
                    break;
                default:
                    throw new Exception("Data type [" . $param['type'] . "] is unknown, please check your data.", 1);
            }
        }

        return $params;
    }

    private function parseEntities($dataset)
    {
        $entities = [];

        try {
            foreach ($dataset as $key1 => $data) {
                $entity = [];
                foreach ($data['param'] as $key2 => $param) {
                    $entity[$param['@attributes']['name']] = null;
                    switch ($param['@attributes']['type']) {
                        case 'ptFloat':
                            if (isset($param['@attributes']['float_val'])) {
                                $entity[$param['@attributes']['name']] = floatval($param['@attributes']['float_val']);
                            }
                            break;
                        case 'ptInt':
                            if (isset($param['@attributes']['int_val'])) {
                                $entity[$param['@attributes']['name']] = $param['@attributes']['int_val'];
                            }
                            break;
                        case 'ptDateTime':
                            if (isset($param['@attributes']['date_val'])) {
                                $entity[$param['@attributes']['name']] = new \DateTime($param['@attributes']['date_val']);
                            }
                            break;
                        case 'ptBool':
                            if (isset($param['@attributes']['bool_val'])) {
                                if ($param['@attributes']['bool_val'] == "false") {
                                    $entity[$param['@attributes']['name']] = false;
                                } else {
                                    $entity[$param['@attributes']['name']] = true;
                                }
                            }
                            break;
                        case 'ptString':
                            if (isset($param[0])) {
                                $entity[$param['@attributes']['name']] = $param[0];
                            }
                            break;
                        default:
                    }
                }
                array_push($entities, $entity);
            }

        } catch (Exception $e) {

        }

        return $entities;
    }
}
