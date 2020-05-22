<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Ancien mot de passe", "Votre mot de passe actuel..."))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Votre nouveau mot de passe..."))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe (confirmation)", "Confirmez votre nouveau mot de passe..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
