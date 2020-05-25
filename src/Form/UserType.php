<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add(
                'roles', 
                ChoiceType::class,
                $this->getConfiguration("Les rôles de cet utilisateur", "", [
                    'choices' => [
                        'Superviseur du client' => 'ROLE_CUSTOMER_SUPERVISOR',
                        'Utilisateur Belair non admin' => 'ROLE_BAI_USER',
                        'Spécial support/hotline' => 'ROLE_BAI_HOTLINE_BASIC',
                        'Spécial développeurs' => 'ROLE_BAI_DEV'
                    ],
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false
                ])
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
