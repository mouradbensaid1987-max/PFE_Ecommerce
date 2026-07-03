<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

      $slugger = new AsciiSlugger();

      $categories = [
        'Informatique',
        'Téléphonie',
        'Vêtements',
        'Maison',
        'Sports',
        'Livres',
        ];

        foreach ($categories as $name) 
          {
              $cat = new Category();
              $cat->setName($name);
              $cat->setSlug(strtolower($slugger->slug($name)));
              $cat->setDescription('Description de la catégorie ' . $name);
              $manager->persist($cat);
          }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
