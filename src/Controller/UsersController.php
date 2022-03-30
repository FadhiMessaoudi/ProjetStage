<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Symfony\Component\Mime\Encoder\EncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'users_')]
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    #[Route('/users/annonces/ajout', name: 'users_annonces_ajout')]
    public function ajoutAnnonce(HttpFoundationRequest $request, ManagerRegistry $doctrine): Response
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);

            $em = $doctrine->getManager();
            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('users_');
        }

        return $this->render('users/annonces/ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users/profil/modifier', name: 'users_profil_modifier')]
    public function editProfile(HttpFoundationRequest $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('message', 'Profil mis à jour');
            return $this->redirectToRoute('users_');
        }

        return $this->render('users/editProfile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users/pass/modifier', name: 'users_pass_modifier')]
    public function editPass(HttpFoundationRequest $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder,): Response
    {
        if ($request->isMethod('POST')) {
            $em = $doctrine->getManager();

            $user = $this->getUser();

            // on vérifie si les 2 mots de passe sont identiques
            if ($request->request->get('pass') == $request->request->get('pass2')) {
                $user->setPassword($passwordEncoder->hashPassword($user, $request->request->get('pass')));
                $em->flush();
                $this->addFlash('message', 'Mot de passe mis à jour avec succès');
                return $this->redirectToRoute('users_');
            } else {
                $this->addFlash('error', 'Les deux mdp ne sont pas identiques');
            }
        }


        return $this->render('users/editPass.html.twig');
    }
}
