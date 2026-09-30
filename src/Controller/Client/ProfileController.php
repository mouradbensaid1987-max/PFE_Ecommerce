<?php

namespace App\Controller\Client; 

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\SecurityBundle\Security;

#[IsGranted('ROLE_USER')]
#[Route('/profile')]
class ProfileController extends AbstractController
{

  #[Route('', name: 'app_profile')]
  public function show(): Response
  {
    return $this->render('profile/show.html.twig', [
    'user' => $this->getUser(),
    ]);
  }



  #[Route('/edit', name: 'app_profile_edit')]
  public function edit(Request $request, EntityManagerInterface $em): Response
  {
      $form = $this->createForm(ProfileFormType::class, $this->getUser());
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) 
        {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }
      return $this->render('profile/edit.html.twig', [
          'form' => $form->createView(),
        ]);
  }
  #[Route('/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        FavoriteRepository $favoriteRepository,
        Security $security,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
 
        // 1) Protection CSRF
        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Action non autorisée.');
            return $this->redirectToRoute('app_profile');
        }
 
        // 2) Confirmation par le mot de passe
        if (!$passwordHasher->isPasswordValid($user, (string) $request->request->get('password'))) {
            $this->addFlash('danger', 'Mot de passe incorrect : votre compte n\'a pas été supprimé.');
            return $this->redirectToRoute('app_profile');
        }
 
        // 3) Suppression des données qui ne sont plus nécessaires
        foreach ($user->getAddresses() as $address) {
            $em->remove($address);
        }
        foreach ($favoriteRepository->findBy(['user' => $user]) as $favorite) {
            $em->remove($favorite);
        }
        foreach ($user->getMessages() as $message) {
            $em->remove($message);
        }
        foreach ($user->getResetPasswordRequests() as $resetRequest) {
            $em->remove($resetRequest);
        }
 
        // 4) Anonymisation du compte (les commandes restent liées, sans données personnelles)
        $user->setEmail('supprime-' . $user->getId() . '@fixpro.invalid');
        $user->setFirstName('Compte');
        $user->setLastName('supprimé');
        $user->setRoles([]);
        $user->setVerified(false);
        $user->setPassword(bin2hex(random_bytes(32)));   // mot de passe impossible à retrouver
 
        $em->flush();
 
        // 5) Déconnexion
        $security->logout(false);
 
        $this->addFlash('success', 'Votre compte a bien été supprimé.');
        return $this->redirectToRoute('app_home');
    }



}