<?php

namespace App\Controller\Client;

use App\Entity\Address;
use App\Form\AddressFormType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/profile/addresses')]
class AddressController extends AbstractController
{

  #[Route('', name: 'app_address_list')]
  public function list(AddressRepository $addressRepository): Response
  {
      $user = $this->getUser();
      $adresses = $addressRepository->findBy(['user' => $user]);
      
      return $this->render('address/list.html.twig', [
      'addresses' => $adresses,
      ]);
  }


  #[Route('/new', name: 'app_address_new')]
  public function new(Request $request, EntityManagerInterface $em): Response
  {
      $address = new Address();
      $user = $this->getUser();

      $address->setUser($user);

      $form = $this->createForm(AddressFormType::class, $address);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) 
        {
            $em->persist($address);
            $em->flush();
            $this->addFlash('success', 'Adresse ajoutée !');

            return $this->redirectToRoute('app_address_list');
        }

      return $this->render('address/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle adresse',
        ]);
  }

  #[Route('/{id}/edit', name: 'app_address_edit')]
  public function edit(Address $address, Request $request, EntityManagerInterface $em): Response
  {
      if ($address->getUser() !== $this->getUser()) 
        {
            throw $this->createAccessDeniedException();
        }
      
      $form = $this->createForm(AddressFormType::class, $address);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) 
        {
            $em->flush();
            $this->addFlash('success', 'Adresse modifiée !');
            return $this->redirectToRoute('app_address_list');
        }

      return $this->render('address/form.html.twig', [
          'form' => $form->createView(),
          'title' => 'Modifier l\'adresse',
      ]);
  }


  #[Route('/{id}/delete', name: 'app_address_delete', methods: ['POST'])]
  public function delete(Address $address, Request $request, EntityManagerInterface $em): Response
  {
    if ($address->getUser() !== $this->getUser()) 
      {
          throw $this->createAccessDeniedException();
      }
    if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) 
      {
        $em->remove($address);
        $em->flush();
        $this->addFlash('success', 'Adresse supprimée');
      }
    return $this->redirectToRoute('app_address_list');
  }

}