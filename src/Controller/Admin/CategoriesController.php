<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @Route("/admin/categories", name="admin_categories_")
 * @package App\Controller\Admin
 */

class CategoriesController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoriesRepository $catrepo): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $catrepo->findAll()
        ]);
    }
    #[Route('/ajout', name: 'ajout')]
    public function ajoutCategorie(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorie = new Categories;

        $form = $this->createForm(CategoriesType::class, $categorie,);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/modif/{id}', name: 'modif')]
    public function modifCategorie(Categories $categorie, Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(CategoriesType::class, $categorie,);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('admin_categories_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
