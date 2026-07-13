<?php

namespace App\Controller\Client; 

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

}