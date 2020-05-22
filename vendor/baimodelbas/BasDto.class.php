<?php

namespace Belair;

use Belair\Bas;
use Exception;
use SoapVar;
use SoapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * BasDto
 */
class BasDto extends Bas
{
    var $Id;
    var $Properties;
    var $TypeName;

    /**
     * 
     * @return void 
     */
    function __construct()
    {
        $this->Id = com_create_guid();
        $this->Properties = new BasParams();
    }

    /**
     * 
     * @return void 
     * @throws ServiceNotFoundException 
     */
    function DebugPrint()
    {
        BasDto::DebugPrintDto($this);
    }

    /**
     * 
     * @param mixed $dto 
     * @return void 
     * @throws ServiceNotFoundException 
     */
    public static function DebugPrintDto($dto)
    {
        if ($dto == null) {
            print "<pre>Object (NULL)</pre>";
            return;
        }
        print "<pre>Object (Type: " . $dto->TypeName . ", Id: " . $dto->Id . "):<br/>";
        $props = null;
        if (property_exists($dto, "Properties")) {
            if (property_exists($dto->Properties, "Items"))
                $props = $dto->Properties->Items;
            else if (property_exists($dto->Properties, "item"))
                $props = $dto->Properties->item;
        }
        if ($props == null) {
            print "\tNo properties found<br/>";
            var_dump($dto);
        } else {
            foreach ($props as $prop) {
                print "\t" . $prop->Name . ": " . (($prop->IsNull) ? "<i>NULL</i>" : BasParam::GetValueStr($prop)) . "<br/>";
            }
        }
        print "</pre>";
    }

    /**
     * 
     * @param mixed $name 
     * @return SoapVar 
     * @throws ServiceNotFoundException 
     */
    public function ToSoapVar($name, $BAS_NS_URI)
    {
        $items = array();
        for ($i = 0; $i < sizeof($this->Properties->Items); $i++) {
            $items[] = BasParam::ToSoapVar($this->Properties->Items[$i], $BAS_NS_URI);
        }
        $items_array = new SoapVar(array("Items" => $items), SOAP_ENC_OBJECT);
        $props_array = new SoapVar(array(
            "Id" => new SoapVar($this->Id, XSD_STRING),
            "TypeName" => new SoapVar($this->TypeName, XSD_STRING),
            "Properties" => new SoapVar($items_array, SOAP_ENC_OBJECT, "BasParams", $this->absController->getParameter('BAS_NS_URI'))
        ), SOAP_ENC_OBJECT);
        $result = new SoapVar($props_array, SOAP_ENC_OBJECT, "BasDto", $this->absController->getParameter('BAS_NS_URI'), $name);
        return $result;
    }

    /**
     * 
     * @param mixed $src 
     * @return null|BasDto 
     * @throws Exception 
     */
    public static function CreateCopy($src)
    {
        if ($src == null)
            return null;
        $result = new BasDto();
        $result->Id = $src->Id;
        $result->TypeName = $src->TypeName;
        $result->Properties = BasParams::CreateCopy($src->Properties);
        return $result;
    }
}