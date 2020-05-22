<?php

namespace App\Entity;

use App\Repository\WebServiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WebServiceRepository::class)
 */
class WebService
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $params = [];

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
