<?php

namespace App\Form;

use App\Entity\Carousel;
use App\Form\ApplicationType;
use App\Form\CarouselElementType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CarouselType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                null,
                $this->getConfiguration("Nom du carousel", "Donnez un nom à votre carousel...")
            )
            ->add(
                'description',
                null,
                $this->getConfiguration("Décrivez l'utilisation de ce carousel", "Un court texte, +/- 100 caractères...")
            )
            ->add(
                'status',
                null,
                $this->getConfiguration("Visible sur le site", "")
            )
            ->add(
                'carouselElements',
                CollectionType::class,
                [
                    'label' => false,
                    'entry_type' => CarouselElementType::class,
                    'allow_add' => true, // autoriser l'ajout de nouveaux éléments
                    'allow_delete' => true // autoriser l'ajout de nouveaux éléments
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Carousel::class,
        ]);
    }
}
