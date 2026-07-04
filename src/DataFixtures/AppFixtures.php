<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tva;
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

        $tvas = [
          ['Standard', '20.00'],
          ['Intermédiaire', '10.00'],
          ['Réduit', '5.50'],
          ['Super réduit', '2.10'],
        ];


        foreach ($tvas as [$name, $rate]) 
          {
              $t = new Tva();
              $t->setName($name);
              $t->setRate($rate);
              $manager->persist($t);
          }


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
