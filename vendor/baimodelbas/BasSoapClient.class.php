<?php

namespace Belair;

use SoapClient;

/**
 * BasSoapClient
 */
class BasSoapClient extends SoapClient
{
    var $ServiceName;

    /**
     * 
     * @return void 
     */
    function PrintTypes()
    {
        $types = $this->__getTypes();
        echo "<h3>" . $this->ServiceName . " types:</h3>";
        echo "<pre>";
        foreach ($types as $type) {
            $type = preg_replace(
                array('/(\w+) ([a-zA-Z0-9]+)/', '/\n /'),
                array('<font color="green">${1}</font> <font color="blue">${2}</font>', "\n\t"),
                $type
            );
            echo $type;
            echo "\n\n";
        }
        echo "</pre>";
    }

    /**
     * 
     * @param mixed $baseUrl 
     * @param mixed $serviceName 
     * @param array $options 
     * @return void 
     */
    public function __construct($baseUrl, $serviceName, $options = array())
    {
        $this->ServiceName = $serviceName;
        parent::__construct($baseUrl . "/wsdl/" . $this->ServiceName, $options);
    }
}
