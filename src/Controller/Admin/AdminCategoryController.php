<?php 

namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/categories')]
class AdminCategoryController extends AbstractController
{

    #[Route('', name: 'app_admin_category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
      $categories = $categoryRepository->findAll();

        return $this->render('admin/category/index.html.twig', [
          'categories' => $categories
          ]);
    }


    #[Route('/new', name: 'app_admin_category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
          $category = new Category();

          $form = $this->createForm(CategoryFormType::class, $category);
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) 
            {
                $em->persist($category);
                $em->flush();
                $this->addFlash('success', 'Catégorie enregistrée');

                return $this->redirectToRoute('app_admin_category_index');
            }

      return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouvelle catégorie',
        ]);
    
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
            {
                $em->flush();
                $this->addFlash('success', 'Catégorie enregistrée');

                return $this->redirectToRoute('app_admin_category_index');
            }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier la catégorie',
        ]);
        
    }


    #[Route('/{id}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function delete(Category $category, Request $request, EntityManagerInterface $em): Response
    {

      if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) 
        {
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie supprimée');
        }

      return $this->redirectToRoute('app_admin_category_index');

    }






}