<?php

namespace App\Controller\Admin;

use App\Entity\Tva;
use App\Form\TvaFormType;
use App\Repository\TvaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;




#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/tva')]
class AdminTvaController extends AbstractController
{

    #[Route('', name: 'app_admin_tva_index')]
    public function index(TvaRepository $tvaRepository): Response
    {
      $tvas = $tvaRepository->findAll();

        return $this->render('admin/tva/index.html.twig', [
          'tvas' => $tvas
          ]);
    }

    #[Route('/new', name: 'app_admin_tva_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
          $tva = new Tva();

          $form = $this->createForm(TvaFormType::class, $tva);
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) 
            {
                $em->persist($tva);
                $em->flush();
                $this->addFlash('success', 'TVA enregistrée');

                return $this->redirectToRoute('app_admin_tva_index');
            }

      return $this->render('admin/tva/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle TVA',
        ]);
    
    }


    #[Route('/{id}/edit', name: 'app_admin_tva_edit')]
    public function edit(Tva $tva, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(TvaFormType::class, $tva);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
            {
                $em->flush();
                $this->addFlash('success', 'TVA enregistrée');

                return $this->redirectToRoute('app_admin_tva_index');
            }

        return $this->render('admin/tva/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier le TVA',
        ]);
        
    }



    #[Route('/{id}/delete', name: 'app_admin_tva_delete', methods: ['POST'])]
    public function delete(Tva $tva, Request $request, EntityManagerInterface $em): Response
    {

      if ($this->isCsrfTokenValid('delete'.$tva->getId(), $request->request->get('_token'))) 
        {
            $em->remove($tva);
            $em->flush();
            $this->addFlash('success', 'TVA supprimée');
        }

      return $this->redirectToRoute('app_admin_tva_index');

    }



}