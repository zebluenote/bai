<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CarouselRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=CarouselRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Carousel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=CarouselElement::class, mappedBy="carousel", cascade="all", orphanRemoval=true)
     */
    private $carouselElements;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->carouselElements = new ArrayCollection();
    }

    /**
     * permet d'initialiser le slug
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void 
     * @throws Exception 
     */
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|CarouselElement[]
     */
    public function getCarouselElements(): Collection
    {
        return $this->carouselElements;
    }

    public function addCarouselElement(CarouselElement $carouselElement): self
    {
        if (!$this->carouselElements->contains($carouselElement)) {
            $this->carouselElements[] = $carouselElement;
            $carouselElement->setCarousel($this);
        }

        return $this;
    }

    public function removeCarouselElement(CarouselElement $carouselElement): self
    {
        if ($this->carouselElements->contains($carouselElement)) {
            $this->carouselElements->removeElement($carouselElement);
            // set the owning side to null (unless already changed)
            if ($carouselElement->getCarousel() === $this) {
                $carouselElement->setCarousel(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
