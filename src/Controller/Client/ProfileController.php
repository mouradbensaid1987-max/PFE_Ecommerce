<?php

namespace App\Controller\Client; 

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


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

}