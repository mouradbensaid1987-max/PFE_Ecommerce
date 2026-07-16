<?php 

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;


#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/products')]
class AdminProductController extends AbstractController
{

    #[Route('', name: 'app_admin_product_index')]
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $productRepository->findAll();

        $produits = $paginator->paginate(
          $data,
          $request->query->getInt('page', 1),
          16
      );

        return $this->render('admin/product/index.html.twig', [
          'products' => $produits
          ]);
    }


    #[Route('/new', name: 'app_admin_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
      $product = new Product();
      $form = $this->createForm(ProductFormType::class, $product);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) 
            {
                $em->persist($product);
                $em->flush();
                $this->addFlash('success', 'Produit enregistré');

                return $this->redirectToRoute('app_admin_product_index');
            }
      
      return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Nouveau produit',
        ]);
    }



    #[Route('/{id}/edit', name: 'app_admin_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em):Response
    {

      $form = $this->createForm(ProductFormType::class, $product);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) 
            {
                $em->flush();
                $this->addFlash('success', 'Produit enregistré');

                return $this->redirectToRoute('app_admin_product_index');
            }
      
      return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modifier le produit',
        ]);

    }


    #[Route('/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, Request $request, EntityManagerInterface $em):Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) 
          {
                $em->remove($product);
                $em->flush();
                $this->addFlash('success', 'Produit supprimé');
          }
      return $this->redirectToRoute('app_admin_product_index');

    }

}