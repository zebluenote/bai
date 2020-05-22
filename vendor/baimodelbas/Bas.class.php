<?php

namespace Belair;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Bas {

    private $absController;

    public function __construct()
    {
        $this->absController = new AbstractController;
    }

}