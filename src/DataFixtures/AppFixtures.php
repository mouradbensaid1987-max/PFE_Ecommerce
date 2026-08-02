<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Tva;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher) 
    {
      
    }

    public function load(ObjectManager $manager): void
    {

      $slugger = new AsciiSlugger();

      $categoryNames = [
          'Vis',
          'Boulons',
          'Écrous',
          'Rondelles',
          'Chevilles',
          'Tiges filetées',
          'Rivets',
          'Clous',
          'Charnières',
          'Serrures',
          'Équerres',
          'Fixations',
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


        
            // === UTILISATEURS ===
          $admin = new User();
          $admin->setEmail('admin@test.com');
          $admin->setFirstName('Admin');
          $admin->setLastName('Boutique');
          $admin->setRoles(['ROLE_ADMIN']);
          $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
          $manager->persist($admin);
        
          $client = new User();
          $client->setEmail('client@test.com');
          $client->setFirstName('Client');
          $client->setLastName('Test');
          $client->setRoles(['ROLE_USER']);
          $client->setPassword($this->hasher->hashPassword($client, 'client123'));
          $manager->persist($client);

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
