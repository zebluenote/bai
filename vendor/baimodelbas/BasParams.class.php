<?php

namespace Belair;

use SoapVar;
use Exception;

/**
 * BasParams
 */
class BasParams extends Bas
{
    var $Items;
    private $absController;

    /**
     * 
     * @return void 
     */
    public function __construct()
    {
        $this->Items = array();
    }


    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public function AddInt($name, $value)
    {
        array_push($this->Items,  BasParam::CreateInt($name, $value));
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public function AddStr($name, $value)
    {
        return $this->AddString($name, $value);
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public function AddString($name, $value)
    {
        array_push($this->Items,  BasParam::CreateString($name, $value));
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public function AddFloat($name, $value)
    {
        array_push($this->Items,  BasParam::CreateFloat($name, $value));
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public function AddDateTime($name, $value)
    {
        array_push($this->Items,  BasParam::CreateDateTime($name, $value));
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $format 
     * @param mixed $value 
     * @return void 
     * @throws Exception 
     */
    public function AddDateTimeFmt($name, $format, $value)
    {
        // var_dump(BasParam::CreateDateTimeFmt($name, $format, $value));
        // die(__FILE__);
        array_push($this->Items,  BasParam::CreateDateTimeFmt($name, $format, $value));
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return void 
     */
    public function AddBool($name, $value)
    {
        array_push($this->Items,  BasParam::CreateBool($name, $value));
    }


    /**
     * 
     * @param mixed $name 
     * @return mixed 
     * @throws Exception 
     */
    public function GetByName($name)
    {
        foreach ($this->Items as $item) {
            if (strcasecmp($item->Name, $name) == 0)
                return $item;
        }
        throw new Exception("Value not found. Name: " . $name);
    }

    /**
     * 
     * @param mixed $src 
     * @return null|BasParams 
     * @throws Exception 
     */
    public static function CreateCopy($src)
    {
        if ($src == null)
            return null;
        $result = new BasParams();
        $result->Items = array();
        $props = null;
        if (property_exists($src, "Items"))
            $props = $src->Items;
        else if (property_exists($src, "item"))
            $props = $src->item;
        if ($props != null) {
            for ($i = 0; $i < sizeof($props); $i++) {
                $result->Items[$i] = BasParam::CreateCopy($props[$i]);
            }
        }
        return $result;
    }

    /**
     * 
     * @param mixed $name 
     * @return SoapVar 
     */
    function ToSoapVar($name, $BAS_NS_URI)
    {
        $items = array();
        for ($i = 0; $i < sizeof($this->Items); $i++) {
            $items[] = BasParam::ToSoapVar($this->Items[$i], $BAS_NS_URI);
        }
        $items_array = new SoapVar(array("Items" => $items), SOAP_ENC_OBJECT);
        $result = new SoapVar($items_array, SOAP_ENC_OBJECT, "BasParams", $BAS_NS_URI, $name);
        return $result;
    }
};
