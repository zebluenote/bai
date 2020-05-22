<?php

namespace App\Form;

use App\Entity\Faq;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FaqType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'num',
                TextType::class,
                $this->getConfiguration("Référence Belair de la Faq", "Le numéro Belair de cette Faq...")
            )
            ->add(
                'title',
                null,
                $this->getConfiguration("Titre de la Faq", "Le titre de cette Faq...")
            )
            ->add(
                'content',
                CKEditorType::class,
                $this->getConfiguration("Contenu de la Faq", "Saisissez le contenu...", [
                    'input_sync' => true
                ])
            )
            ->add('contentFormat', ChoiceType::class, [
                'label' => ' ',
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'choices' => [
                    'text' => 'text',
                    'html' => 'html'
                ] 
            ])
            ->add(
                'format',
                null,
                $this->getConfiguration("Format (text, html)", "Le format du contenu...", [
                    'attr' => [
                        'required' => true,
                        'readonly' => true
                    ]
                ])
            )
            ->add(
                'category',
                null,
                $this->getConfiguration("Choisissez une catégorie", "")
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Faq::class,
        ]);
    }
}
