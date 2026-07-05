<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tva;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

      $slugger = new AsciiSlugger();

      $categoryNames = [
        'Informatique',
        'Téléphonie',
        'Vêtements',
        'Maison',
        'Sports',
        'Livres',
        ];

        $categories = [];

        foreach ($categoryNames as $name) 
          {
              $cat = new Category();
              $cat->setName($name);
              $cat->setSlug(strtolower($slugger->slug($name)));
              $cat->setDescription('Description de la catégorie ' . $name);
              $manager->persist($cat);
              $categories[$name] = $cat;
          }

        $tvaData = [
          ['Standard', '20.00'],
          ['Intermédiaire', '10.00'],
          ['Réduit', '5.50'],
          ['Super réduit', '2.10'],
        ];
        $tvas = [];

        foreach ($tvaData as [$name, $rate]) 
          {
              $t = new Tva();
              $t->setName($name);
              $t->setRate($rate);
              $manager->persist($t);
              $tvas[] = $t;
          }
          $tvaStandard = $tvas[0];


        $baseProducts = [
            ['Laptop Pro 15"', 'Informatique', 999.00, 10],
            ['Souris sans fil', 'Informatique', 24.90, 50],
            ['Clavier mécanique', 'Informatique', 89.00, 25],
            ['Casque audio Bluetooth', 'Informatique', 59.00, 30],
            ['Smartphone X10', 'Téléphonie', 399.00, 20],
            ['Coque silicone', 'Téléphonie', 12.00, 100],
            ['Chargeur rapide USB-C', 'Téléphonie', 18.50, 80],
            ['T-shirt coton bio', 'Vêtements', 19.99, 60],
            ['Jean slim', 'Vêtements', 49.00, 40],
            ['Sweat à capuche', 'Vêtements', 35.00, 35],
            ['Lampe de bureau LED', 'Maison', 29.00, 25],
            ['Plante artificielle', 'Maison', 14.50, 50],
            ['Ballon de foot', 'Sports', 22.00, 30],
            ['Raquette de tennis', 'Sports', 79.00, 15],
            ['Roman policier', 'Livres', 9.90, 100],
            ['Manuel de cuisine', 'Livres', 24.00, 40],
          ];

          $products = [];

            for ($i = 1; $i <= 300; $i++) {
                $product = $baseProducts[($i - 1) % count($baseProducts)];

                $products[] = [
                    $product[0] . " $i",     // Nom unique
                    $product[1],             // Catégorie
                    $product[2],             // Prix
                    rand(1, 100),            // Stock aléatoire
                ];
            }

            foreach ($products as [$name, $catName, $price, $stock]) {
                  $p = new Product();
                  $p->setName($name);
                  $p->setSlug(strtolower($slugger->slug($name)));
                  $p->setDescription('Description complète de ' . $name . '. Lorem ipsum dolor sit amet.');
                  $p->setPriceHt((string) $price);
                  $p->setStock($stock);
                  $p->setIsActive(true);
                  $p->setCategory($categories[$catName]);
                  $p->setTva($tvaStandard);
                  $manager->persist($p);
                }
        


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
