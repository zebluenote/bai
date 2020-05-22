<?php

namespace Belair;

use Exception;
use SoapVar;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * 
 * @package Belair
 */
class BasParam extends Bas
{

    var $Name;
    var $DataType;
    var $IsNull;
    // Values
    var $BoolVal;
    var $DateTimeVal;
    var $FloatVal;
    var $IntVal;
    var $StrVal;

    private $absController;

    /**
     * 
     * @param mixed $name 
     * @param mixed $dataType 
     * @param mixed $value 
     * @return void 
     * @throws ServiceNotFoundException 
     * @throws Exception 
     */
    function __construct($name, $dataType, $value)
    {
        $this->Name = $name;
        $this->DataType = $dataType;
        $this->IsNull = false;
        switch ($dataType) {
            case "basParamInt":
                $this->IntVal = $value;
                break;
            case "basParamString":
                $this->StrVal = $value;
                break;
            case "basParamFloat":
                $this->FloatVal = $value;
                break;
            case "basParamDateTime":
                if ($value != null) {
                    if (is_string($value)) {
                        $this->DateTimeVal = \DateTime::createFromFormat( $this->absController->getParameter('BAS_DATETIME_FMT'), $value);
                    } else {
                        $this->DateTimeVal = clone $value;
                    }
                } else
                    $this->DateTimeVal = null;
                break;
            case "basParamBool":
                $this->BoolVal = $value;
                break;
            default:
                throw new Exception("Unsupported type: " . $dataType);
        }
    }

    /**
     * 
     * @param mixed $prop 
     * @return mixed|void 
     */
    static function GetValue($prop)
    {
        switch ($prop->DataType) {
            case "basParamInt":
                return $prop->IntVal;
            case "basParamString":
                return $prop->StrVal;
            case "basParamFloat":
                return $prop->FloatVal;
            case "basParamDateTime":
                return $prop->DateTimeVal;
            case "basParamBool":
                return $prop->BoolVal;
            default:
                return $prop->StrVal;
        }
    }

    /**
     * 
     * @param mixed $prop 
     * @return mixed|void|string 
     * @throws ServiceNotFoundException 
     */
    static function GetValueStr($prop)
    {
        if ($prop == null)
            return "NULL";
        $val = BasParam::GetValue($prop);
        if ($prop->IsNull)
            return "NULL";
        if ($val instanceof \DateTime)
            return $val->format($this->absController->getParameter('BAS_DATETIME_FMT'));
        else if (is_bool($val))
            return $val ? "true" : "false";
        return $val;
    }


    /**
     * 
     * @param mixed $src 
     * @return mixed|BasParam 
     * @throws Exception 
     */
    public static function CreateCopy($src)
    {
        switch ($src->DataType) {
            case "basParamInt":
                $result = new BasParam($src->Name, $src->DataType, $src->IntVal);
                break;
            case "basParamString":
                $result = new BasParam($src->Name, $src->DataType, $src->StrVal);
                break;
            case "basParamFloat":
                $result = new BasParam($src->Name, $src->DataType, $src->FloatVal);
                break;
            case "basParamDateTime":
                $result = new BasParam($src->Name, $src->DataType, $src->DateTimeVal);
                break;
            case "basParamBool":
                $result = new BasParam($src->Name, $src->DataType, $src->BoolVal);
                break;
            default:
                throw new Exception("Unsupported type: " . $src->DataType);
        }
        $result->IsNull = $src->IsNull;
        return $result;
    }


    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return BasParam 
     */
    public static function CreateInt($name, $value)
    {
        return new BasParam($name, "basParamInt", $value);
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return BasParam 
     */
    public static function CreateString($name, $value)
    {
        return new BasParam($name, "basParamString", $value);
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return BasParam 
     */
    public static function CreateFloat($name, $value)
    {
        return new BasParam($name, "basParamFloat", $value);
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return BasParam 
     */
    public static function CreateDateTime($name, $value)
    {
        return new BasParam($name, "basParamDateTime", $value);
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $format 
     * @param mixed $value 
     * @return BasParam 
     * @throws ServiceNotFoundException 
     * @throws Exception 
     */
    public static function CreateDateTimeFmt($name, $format, $value)
    {
        $date = \DateTime::createFromFormat($format, $value);
        if (!$date)
            throw new Exception("Invalid date: " . $value . ". Format: " . $format . " CONFIG=" . $this->absController->getParameter('BAS_DATETIME_FMT'));
        return new BasParam($name, "basParamDateTime", $date);
    }

    /**
     * 
     * @param mixed $name 
     * @param mixed $value 
     * @return BasParam 
     */
    public static function CreateBool($name, $value)
    {
        return new BasParam($name, "basParamBool", $value);
    }

    /**
     * 
     * @param mixed $param 
     * @return void 
     */
    public static function DebugPrintParam($param)
    {
        if ($param == null) {
            print "<p>Param is NULL</p>";
            return;
        }
        print "<pre>Param (Name: " . $param->Name . ", type: " . $param->DataType . "):<br/>";
        print "\tIntVal: " . $param->IntVal . "<br/>";
        print "\tStrVal: " . $param->StrVal . "<br/>";
        print "\tFloatVal: " . $param->FloatVal . "<br/>";
        print "\tDateTimeVal: " . $param->DateTimeVal . "<br/>";
        print "\tBoolVal: " . $param->BoolVal . "<br/>";
        print "\tIsNull: " . $param->IsNull . "<br/>";
        print "</pre>";
    }

    /**
     * 
     * @return void 
     */
    public function DebugPrint()
    {
        BasParam::DebugPrintParam($this);
    }

    /**
     * 
     * @param mixed $param 
     * @return SoapVar 
     * @throws ServiceNotFoundException 
     */
    public static function ToSoapVar($param, $BAS_NS_URI)
    {
        $attrs = array(
            "Name"        => new SoapVar($param->Name, XSD_STRING),
            "DataType"    => new SoapVar($param->DataType, SOAP_ENC_OBJECT, "BasParamDataType", $BAS_NS_URI),
            "IsNull"      => new SoapVar($param->IsNull, XSD_BOOLEAN),
            // Values
            "BoolVal"     => new SoapVar($param->BoolVal, XSD_BOOLEAN),
            "DateTimeVal" => new SoapVar($param->DateTimeVal == null ? null : $param->DateTimeVal->format(DATE_W3C), XSD_DATETIME),
            "FloatVal"    => new SoapVar($param->FloatVal, XSD_DOUBLE),
            "IntVal"      => new SoapVar($param->IntVal, XSD_LONG),
            "StrVal"      => new SoapVar($param->StrVal, XSD_STRING)
        );
        return new SoapVar($attrs, SOAP_ENC_OBJECT, "BasParam", $BAS_NS_URI, "Item");
    }
};
