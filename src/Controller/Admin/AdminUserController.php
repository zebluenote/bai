<?php

namespace App\Controller\Admin;

use LogicException;
use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use App\Service\CustomerHelper;
use App\Service\PaginationHelper;
use App\Repository\CustomerRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin-user-index/{page}", name="admin_user_index", requirements={"page" : "\d+"})
     */
    public function index(CustomerHelper $customerHelper, $page = 1, PaginationHelper $pagination)
    {

        // Appel au service de pagination PaginationHelper
        $pagination
            ->setEntityClass(User::class)
            ->setCurrentPage($page)
            ->setLimit(10)
        ;

        // Appel au service CustomerHelper pour récupérer une liste des clients triée spécifiquement
        $customers = $customerHelper->reArrangeCustomerList();

        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'AdminUserController',
            'customers' => $customers,
            'pagination' => $pagination
        ]);
    }

    /**
     * Modification d'un utilisateur
     * 
     * @Route("admin-user-edit/{id}", name="admin_user_edit", requirements={"id" : "\d+"})
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param User $user 
     * @return Response 
     * @throws LogicException 
     */
    public function edit(User $user, Request $request, EntityManagerInterface $manager, RoleRepository $roleRepository)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $request->request->get('user');
            foreach ( $data['roles'] as $role ) {
                if ( $role === "ROLE_USER" ) {
                    continue;
                }
                // dd($role);
                $r = $roleRepository->findOneByTitle($role);
                // dd($r);
                $user->addUserRole($r);
            }
            // dd($user);
            $manager->persist($user);
            $manager->flush();

        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
