<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/users')]
class AdminUserController extends AbstractController
{

  #[Route('', name: 'app_admin_user_index')]
  public function index(UserRepository $userRepository): Response
  {
      $users = $userRepository->findAll();
      return $this->render('admin/user/index.html.twig', [
        'users' => $users
        ]);
  }


  #[Route('/{id}/edit', name: 'app_admin_user_edit')]
  public function edit(User $user, Request $request, EntityManagerInterface $em): Response
  {
      $form = $this->createForm(UserFormType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid())
        {
          $em->flush();
          $this->addFlash('success', 'Utilisateur modifié');
          return $this->redirectToRoute('app_admin_user_index');

        }
      return $this->render('admin/user/form.html.twig', [
        'form' => $form->createView(), 
        'user'=> $user]);


  }


}