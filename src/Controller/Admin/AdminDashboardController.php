<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin-dashboard", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager)
    {
        // Récupération du nombre d'utilisateurs
        $nbUsers = $manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
        $nbCustomers = $manager->createQuery('SELECT COUNT(c) FROM App\Entity\Customer c')->getSingleScalarResult();
        $nbFaqs = $manager->createQuery('SELECT COUNT(f) FROM App\Entity\Faq f')->getSingleScalarResult();
        $nbWebServices = $manager->createQuery('SELECT COUNT(w) FROM App\Entity\WebService w')->getSingleScalarResult();

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => compact('nbUsers', 'nbCustomers', 'nbFaqs', 'nbWebServices')
        ]);
    }
}
