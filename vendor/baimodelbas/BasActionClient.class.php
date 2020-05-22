<?php

namespace Belair;

/**
 * 
 * @package Belair
 */
class BasActionClient extends BasSoapClient
{
    public function __construct($baseUrl, $options = array())
    {
        parent::__construct($baseUrl,  "IBasActionService", $options);
    }
}
