<?php

namespace App\Controller;

use LogicException;
use App\Entity\Customer;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    
    /**
     * affichage du formulaire de login
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response 
     * @throws LogicException 
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     * 
     * @return void 
     */
    public function logout()
    {
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * 
     * @Route("/account-show", name="account_show")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response 
     * @throws LogicException 
     */
    public function myAccountShow(CustomerRepository $customerRepo)
    {
        $user = $this->getUser();

        $companyId = $user->getCustomer()->getId();
        $customer =$customerRepo->findOneBy(['id' => $companyId]);
        
        return $this->render('account/profile.html.twig', [
            'user' => $user,
            'customer' => $customer
        ]);
    }

    /**
     * Permet à l'utilisateur connecté de modifier son profil 
     * 
     * @Route("/account-edit", name="account_edit")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response 
     * @throws LogicException 
     */
    public function myAccountEdit(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', "Les informations de cotre compte ont été modifiées");
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * permet à l'utilisateur connecté de modifier son mot de passe
     * 
     * @Route("/account-password-update", name="account_password_update")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @param Request $request 
     * @param EntityManagerInterface $manager 
     * @return Response 
     * @throws LogicException 
     */
    public function passwordUpdate(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On vérifie que l'ancien password est bien valide
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                // Gérer l'erreur : on va faire apparaitre une erreur au niveau du champ oldPassword
                // en utilisant l'Api Symfony
                $form->get('oldPassword')->addError(new FormError("Ce mot de passe n'est pas votre mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);
    
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', "Votre mot de passe a été modifié");
                return $this->redirectToRoute("homepage");
   
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
