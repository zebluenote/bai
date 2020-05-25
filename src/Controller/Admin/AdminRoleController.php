<?php

namespace App\Controller\Admin;

use App\Repository\RoleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminRoleController extends AbstractController
{
    /**
     * @Route("/admin-role-index", name="admin_role_index")
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(RoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();

        return $this->render('admin/role/index.html.twig', [
            'controller_name' => 'AdminRoleController',
            'roles' => $roles
        ]);
    }
}
