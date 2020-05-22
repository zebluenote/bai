<?php

namespace Belair;

/**
 * 
 * @package Belair
 */
class BasObjectAccessClient extends BasSoapClient
{
    public function __construct($baseUrl, $options = array())
    {
        parent::__construct($baseUrl, "IBasObjectAccessService", $options);
    }
}
