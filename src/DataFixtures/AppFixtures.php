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

        // === CATALOGUE QUINCAILLERIE / VISSERIE ===
        // [nom, categorie, materiau, diametre, longueur, typeTete, typeEmpreinte, unite, qteCond, prix, stock]

        $quincaillerieProducts = [

        // -- Vis --
        ['Vis à bois tête fraisée 4x30mm', 'Vis', 'Acier zingué', '4mm', 30, 'Fraisée', 'Cruciforme PZ2', 'boite', 100, 6.90, 40],
        ['Vis à bois tête fraisée 5x50mm', 'Vis', 'Acier zingué', '5mm', 50, 'Fraisée', 'Cruciforme PZ2', 'boite', 50, 8.90, 35],
        ['Vis métaux tête cylindrique M4x20', 'Vis', 'Acier inoxydable A2', 'M4', 20, 'Cylindrique', 'Cruciforme PZ2', 'sachet', 50, 5.90, 40],

        // -- Boulons --
        ['Boulon hexagonal M8x60', 'Boulons', 'Acier inoxydable A2', 'M8', 60, 'Hexagonale', null, 'sachet', 10, 8.20, 25],
        ['Boulon hexagonal M10x80', 'Boulons', 'Acier zingué', 'M10', 80, 'Hexagonale', null, 'sachet', 10, 9.90, 20],
        ['Boulon à tête carrée M8x50', 'Boulons', 'Acier zingué', 'M8', 50, 'Carrée', null, 'sachet', 10, 8.90, 18],

        // -- Écrous --
        ['Écrou hexagonal M8', 'Écrous', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 20, 3.50, 60],
        ['Écrou hexagonal M10', 'Écrous', 'Acier zingué', 'M10', null, null, null, 'sachet', 20, 3.90, 50],
        ['Écrou papillon M6', 'Écrous', 'Acier zingué', 'M6', null, null, null, 'sachet', 20, 3.20, 40],

        // -- Rondelles --
        ['Rondelle plate M8', 'Rondelles', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 50, 2.90, 70],
        ['Rondelle grower M8', 'Rondelles', 'Acier zingué', 'M8', null, null, null, 'sachet', 50, 2.50, 60],
        ['Rondelle plate M10', 'Rondelles', 'Acier zingué', 'M10', null, null, null, 'sachet', 50, 3.10, 55],

        // -- Chevilles --
        ['Cheville nylon à expansion 6x30mm', 'Chevilles', 'Nylon', '6mm', 30, null, null, 'sachet', 25, 3.90, 55],
        ['Cheville nylon à expansion 8x40mm', 'Chevilles', 'Nylon', '8mm', 40, null, null, 'sachet', 25, 4.90, 50],
        ['Cheville Molly métallique M5', 'Chevilles', 'Acier', 'M5', null, null, null, 'sachet', 10, 6.50, 30],
        
        // -- Tiges filetées --
        ['Tige filetée M8x1000mm', 'Tiges filetées', 'Acier zingué', 'M8', 1000, null, null, 'piece', null, 6.90, 25],
        ['Tige filetée M10x1000mm', 'Tiges filetées', 'Acier zingué', 'M10', 1000, null, null, 'piece', null, 8.50, 20],
        ['Tige filetée inox M6x1000mm', 'Tiges filetées', 'Acier inoxydable A2', 'M6', 1000, null, null, 'piece', null, 9.90, 15],
        
        // -- Rivets --
        ['Rivet aveugle alu 4x10mm', 'Rivets', 'Aluminium', '4mm', 10, null, null, 'boite', 100, 5.90, 30],
        ['Rivet aveugle alu 4.8x16mm', 'Rivets', 'Aluminium', '4.8mm', 16, null, null, 'boite', 100, 6.90, 25],
        ['Rivet inox 4x12mm', 'Rivets', 'Acier inoxydable A2', '4mm', 12, null, null, 'boite', 50, 8.90, 18],
        
        // -- Clous --
        ['Clou acier tête plate 2.5x50mm', 'Clous', 'Acier', '2.5mm', 50, 'Plate', null, 'kg', 1, 5.90, 45],
        ['Pointe tête homme 3x70mm', 'Clous', 'Acier', '3mm', 70, 'Plate', null, 'kg', 1, 6.50, 30],
        ['Clou galvanisé 4x100mm', 'Clous', 'Acier galvanisé', '4mm', 100, 'Plate', null, 'kg', 1, 7.20, 22],

        // -- Charnières --
        ['Charnière plate 60mm inox', 'Charnières', 'Acier inoxydable A2', null, 60, null, null, 'piece', null, 3.90, 40],
        ['Charnière piano 1m', 'Charnières', 'Acier zingué', null, 1000, null, null, 'piece', null, 12.90, 15],
        ['Charnière invisible 100mm', 'Charnières', 'Acier zingué', null, 100, null, null, 'piece', null, 6.50, 25],
        
        // -- Serrures --
        ['Serrure en applique 3 points', 'Serrures', 'Acier', null, null, null, null, 'piece', null, 49.90, 10],
        ['Serrure à mortaiser 60mm axe 40mm', 'Serrures', 'Acier', null, 60, null, null, 'piece', null, 29.90, 15],
        ['Cylindre de serrure 30x30mm', 'Serrures', 'Laiton', null, null, null, null, 'piece', null, 19.90, 20],
        
        // -- Équerres --
        ['Équerre plate renforcée 40x40mm', 'Équerres', 'Acier zingué', null, 40, null, null, 'piece', null, 1.90, 100],
        ['Équerre d\'assemblage 60x60mm', 'Équerres', 'Acier zingué', null, 60, null, null, 'piece', null, 2.50, 80],
        ['Équerre invisible 100mm', 'Équerres', 'Acier', null, 100, null, null, 'piece', null, 3.90, 50],
        
        // -- Fixations --
        ['Kit de fixation murale universelle', 'Fixations', 'Acier / nylon', null, null, null, null, 'boite', 1, 9.90, 20],
        ['Collier de fixation pour tube 20mm', 'Fixations', 'Acier zingué', '20mm', null, null, null, 'piece', null, 1.50, 100],
        ['Patte de fixation réglable', 'Fixations', 'Acier zingué', null, null, null, null, 'piece', null, 4.90, 40]

    ];


    foreach ($quincaillerieProducts as [$name, $catName, $materiau, $diametre, $longueur, $typeTete, $typeEmpreinte, $unite, $qteCond, $price, $stock]) 
      {
            $p = new Product();
            $p->setName($name);
            $p->setSlug(strtolower($slugger->slug($name)));
            $p->setDescription('Description complète de ' . $name . '. Article de quincaillerie / visserie de qualité professionnelle.');
            $p->setPriceHt((string) $price);
            $p->setStock($stock);
            $p->setIsActive(true);
            $p->setCategory($categories[$catName]);
            $p->setTva($tvaStandard);
            $p->setMateriau($materiau);
            $p->setDiametreMm($diametre);
            $p->setLongueurMm($longueur);
            $p->setTypeTete($typeTete);
            $p->setTypeEmpreinte($typeEmpreinte);
            $p->setUnite($unite);
            $p->setQuantiteConditionnement($qteCond);
            $manager->persist($p);
      }












        
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
