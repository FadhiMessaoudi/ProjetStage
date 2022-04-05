<?php

namespace App\Controller\Admin;

use App\Entity\Annonces;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;
use App\Form\AnnoncesType;
use App\Form\CategoriesType;
use App\Repository\AnnoncesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @Route("/admin/annonces", name="admin_annonces_")
 * @package App\Controller\Admin
 */

class AnnoncesController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AnnoncesRepository $annoncesrepo): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
            'annonces' => $annoncesrepo->findAll()
        ]);
    }
    #[Route('/ajout', name: 'ajout')]
    public function ajoutAnnonce(Request $request, ManagerRegistry $doctrine): Response
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce,);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('admin_annonces_home');
        }

        return $this->render('admin/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/modif/{id}', name: 'modif')]
    public function modifAnnonce(Annonces $annonce, Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(CategoriesType::class, $annonce,);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('admin_annonces_home');
        }

        return $this->render('admin/annonces/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
