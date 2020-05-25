<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use LimitIterator;

/**
 * Service permettant de faciliter les sytèmes de pagination selon l'entité sur laquelle on travaille
 *
 * @package App\Service
 */
class PaginationHelper
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getPage()
    {
        return $this->currentPage;        
    }

    public function setPage($page)
    {
        $this->currentPage = $page;
        return $this;
    }

    public function getNbPages()
    {
        // Trouver le nombre total d'enregistrements de la table correspondant à cette entité
        $repository = $this->manager->getRepository($this->entityClass);
        $total = count($repository->findAll());

        // Calculer le nombre de pages nécessaire pour tout afficher
        $nbPages = ceil($total/$this->limit);

        return $nbPages;
    }

    public function getData()
    {
        // Calcul de l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au repository de trouver les éléments
        $repository = $this->manager->getRepository($this->entityClass);
        $data = $repository->findBy([], [], $this->limit, $offset);

        // Retourner les éléments de pagination
        return $data;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }
}