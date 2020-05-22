<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ImageType extends AbstractType
{



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $destinations = [];
        foreach ($options['destinations'] as $dest) {
            $destinations[$dest] = $dest;
        }
        $builder
            ->add('destination', ChoiceType::class, [
                'label' => "Sélectionnez la destination",
                'choices' => $destinations
            ])
            ->add('uploadedFile', FileType::class, [
                'label' => 'Image (JPG/PNG file)',

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
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG file',
                    ])
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'destinations' => []
        ]);
    }
}
