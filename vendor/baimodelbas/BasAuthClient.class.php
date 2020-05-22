<?php

namespace Belair;

/**
 * 
 * @package Belair
 */
class BasAuthClient extends BasSoapClient
{
    public function __construct($baseUrl, $options = array())
    {
        parent::__construct($baseUrl,  "IBasAuthService", $options);
    }
}
