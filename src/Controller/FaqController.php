<?php

namespace App\Controller;

use Exception;
use App\Entity\Faq;
use LogicException;
use App\Form\FaqType;
use Symfony\Component\Form\Form;
use App\Repository\FaqRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FaqCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FaqController extends AbstractController
{
    /**
     * Liste de toutes les faqs
     * 
     * @Route("/faqs", name="faqs")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @param FaqRepository $faqRepo 
     * @param FaqCategoryRepository $categoryRepo 
     * @return Response 
     * @throws LogicException 
     */
    public function index(FaqRepository $faqRepo, FaqCategoryRepository $categoryRepo)
    {
        $result = [];
        $categories = $categoryRepo->findAll();
        // dd($categories);
        foreach ($categories as $cat) {
            $catId = $cat->getId();
            $faqs = $faqRepo->findByCategory($cat);
            $result[$catId] = $faqs;
            unset($faqs);
        }
        return $this->render('faq/index.html.twig', [
            'categories' => $categories,
            'faqs' => $result
        ]);
    }

    /**
     * Affichage du détail d'une FAQ
     * 
     * @Route("/faq-detail/{id}", name="faq_show")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @param Faq $faq 
     * @param FaqRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function show(Faq $faq, FaqRepository $repo)
    {
        $faq = $repo->find($faq);
        return $this->render('faq/show.html.twig', [
            'faq' => $faq
        ]);
    }

    /**
     * ADMIN - Liste de toutes les faqs
     * 
     * @Route("/admin-faqs-list", name="admin_faqs_index")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Faq $faq 
     * @param FaqRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function adminList (FaqRepository $faqRepo, FaqCategoryRepository $categoryRepo)
    {
        $result = [];
        $categories = $categoryRepo->findAll();
        // dd($categories);
        foreach ($categories as $cat) {
            $catId = $cat->getId();
            $faqs = $faqRepo->findOrderByNum($cat);
            $result[$catId] = $faqs;
            unset($faqs);
        }
        return $this->render('admin/faq/index.html.twig', [
            'categories' => $categories,
            'faqs' => $result
        ]);
    }

    /**
     * ADMIN - Affichage du détail d'une FAQ
     * 
     * @Route("/admin-faq-detail/{id}", name="admin_faq_show")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Faq $faq 
     * @param FaqRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function adminShow(Faq $faq, FaqRepository $repo)
    {
        $faq = $repo->find($faq);
        return $this->render('admin/faq/show.html.twig', [
            'faq' => $faq
        ]);
    }

    /**
     * ADMIN - Modification d'une FAQ
     * 
     * @Route("/admin-faq-edit/{id}", name="admin_faq_edit")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Faq $faq 
     * @param FaqRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function adminEdit(Faq $faq, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(FaqType::class, $faq);

        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($faq);
                $manager->flush();
                $this->addFlash('success', "Les informations de la Faq {$faq->getNum()} ont été modifiées");
                return $this->redirectToRoute('admin_faq_show', ['id' => $faq->getId()]);
            }
        } catch (Exception $e) {
            $this->addFlash('danger', "Une anomalie a empêché la modification de la Faq");
        }

        return $this->render('admin/faq/edit.html.twig', [
            'form' => $form->createView(),
            'faq' => $faq
        ]);
    }

    /**
     * ADMIN - Création d'une nouvelle FAQ
     * 
     * @Route("/admin-faq-new", name="admin_faq_new")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param Faq $faq 
     * @param FaqRepository $repo 
     * @return Response 
     * @throws LogicException 
     */
    public function adminNew(Request $request, EntityManagerInterface $manager)
    {
        $faq = new Faq();

        $form = $this->createForm(FaqType::class, $faq);

        $form->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $manager->persist($faq);
                $manager->flush();
                $this->addFlash('success', "La Faq {$faq->getNum()} a été enregistrée avec succès");
                return $this->redirectToRoute('admin_faq_show', ['id' => $faq->getId()]);
            }
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->render('admin/faq/edit.html.twig', [
            'form' => $form->createView(),
            'faq' => $faq
        ]);
    }

}
