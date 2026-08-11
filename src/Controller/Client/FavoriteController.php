<?php 

namespace App\Controller\Client;

use App\Entity\Favorite;
use App\Entity\Product;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_USER')]
class FavoriteController extends AbstractController
{
  #[Route('/favorites', name: 'app_favorites')]
  public function index(FavoriteRepository $repo): Response
  {
      $favoriteIds = [];
        if ($this->getUser()) {
            $favoriteIds = $repo->produit_favorie_user($this->getUser());
        }

      $favorites = $repo->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']);
      return $this->render('favorite/list.html.twig', [
        'favorites' => $favorites,
        'favoriteIds' => $favoriteIds,
        ]);
  }


  #[Route('/favorite/toggle/{id}', name: 'app_favorite_toggle', methods: ['POST'])]
  public function toggle(Product $product, FavoriteRepository $repo, EntityManagerInterface $em): Response 
  {

      $existing = $repo->findOneBy(['user' => $this->getUser(),'product' => $product]);

      if ($existing) 
        {
              $em->remove($existing);
              $this->addFlash('info', '💔 Retiré des favoris');

        } else 
        {
              $fav = new Favorite();
              $fav->setUser($this->getUser());
              $fav->setProduct($product);
              $em->persist($fav);
              $this->addFlash('success', '❤️ Ajouté aux favoris');
        }
    $em->flush();

    return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
  }

}