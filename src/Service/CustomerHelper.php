<?php

namespace App\Service;

use App\Repository\CustomerRepository;

/**
 * Permet de travailler sur les clients (entity Customer)
 * 
 * @package App\Service
 */
class CustomerHelper
{
    private $repo;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->repo = $customerRepository;
    }

    public function reArrangeCustomerList()
    {
        $tmp = $this->repo->findAll();
        $customers = [];
        foreach ( $tmp as $customer ) {
            $customers[$customer->getId()] = $customer;
        }
        return $customers;

    }

}