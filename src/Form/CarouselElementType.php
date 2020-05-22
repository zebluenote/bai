<?php

namespace App\Form;

use App\Entity\CarouselElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CarouselElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                null,
                [
                    'attr' => [
                        'placeholder' => "Titre de l'image..."
                    ] 
                ]
            )
            ->add(
                'description',
                null,
                [
                    'attr' => [
                        'placeholder' => "Descriptif de l'image..."
                    ]
                ]
            )
            ->add(
                'image',
                null,
                [
                    'attr' => [
                        'placeholder' => "L'image que vous voulez afficher..."
                    ]
                ]
            )

            ->add('fileupload', FileType::class, [
                'label' => 'Brochure (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '5000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Formats JPEG et PNG uniquement',
                    ])
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CarouselElement::class,
        ]);
    }
}
