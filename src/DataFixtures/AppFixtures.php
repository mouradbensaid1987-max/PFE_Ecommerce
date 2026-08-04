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

            //[nom, categorie, materiau, diametre, longueur, typeTete, typeEmpreinte, unite, qteCond, prix, stock]

            // ===== VIS =====
            ['Vis à bois tête fraisée 4x30mm', 'Vis', 'Acier zingué', '4mm', 30, 'Fraisée', 'Cruciforme PZ2', 'boite', 500, 6.90, 40],
            ['Vis à bois tête fraisée 5x50mm', 'Vis', 'Acier zingué', '5mm', 50, 'Fraisée', 'Cruciforme PZ2', 'boite', 300, 8.90, 35],
            ['Vis métaux tête cylindrique M4x20', 'Vis', 'Acier inoxydable A2', 'M4', 20, 'Cylindrique', 'Torx T25', 'sachet', 50, 5.90, 40],
            ['Vis à placo tête trompette 3.5x25mm', 'Vis', 'Acier phosphaté', '3.5mm', 25, 'Trompette', 'Cruciforme PZ2', 'boite', 1000, 14.90, 15],
            ['Vis à terrasse Torx 5x60mm', 'Vis', 'Acier inoxydable A2', '5mm', 60, 'Fraisée', 'Torx T25', 'boite', 100, 24.90, 12],

            // ===== BOULONS =====
            ['Boulon hexagonal M6x40', 'Boulons', 'Acier inoxydable A2', 'M6', 40, 'Hexagonale', null, 'sachet', 10, 5.90, 30],
            ['Boulon hexagonal M8x60', 'Boulons', 'Acier inoxydable A2', 'M8', 60, 'Hexagonale', null, 'sachet', 10, 8.20, 25],
            ['Boulon d\'ancrage M10x100', 'Boulons', 'Acier zingué', 'M10', 100, 'Hexagonale', null, 'sachet', 5, 13.50, 15],
            ['Boulon à œil M8x60', 'Boulons', 'Acier inoxydable A2', 'M8', 60, 'Œil', null, 'piece', null, 4.90, 25],
            ['Boulon de structure M12x60 haute résistance', 'Boulons', 'Acier classe 8.8', 'M12', 60, 'Hexagonale', null, 'sachet', 5, 15.90, 12],

            // ===== ÉCROUS =====
            ['Écrou hexagonal M6', 'Écrous', 'Acier inoxydable A2', 'M6', null, null, null, 'sachet', 30, 2.90, 60],
            ['Écrou hexagonal M8', 'Écrous', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 20, 3.50, 60],
            ['Écrou papillon M6', 'Écrous', 'Acier zingué', 'M6', null, null, null, 'sachet', 20, 3.20, 40],
            ['Écrou autofreiné (nylstop) M8', 'Écrous', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 20, 4.90, 35],
            ['Écrou borgne M8', 'Écrous', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 20, 4.50, 30],

            // ===== RONDELLES =====
            ['Rondelle plate M8', 'Rondelles', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 50, 2.90, 70],
            ['Rondelle grower M8', 'Rondelles', 'Acier zingué', 'M8', null, null, null, 'sachet', 50, 2.50, 60],
            ['Rondelle éventail M8', 'Rondelles', 'Acier inoxydable A2', 'M8', null, null, null, 'sachet', 50, 3.20, 35],
            ['Rondelle carrossier M8', 'Rondelles', 'Acier zingué', 'M8', null, null, null, 'sachet', 50, 3.50, 30],
            ['Rondelle d\'étanchéité M8', 'Rondelles', 'Caoutchouc / acier', 'M8', null, null, null, 'sachet', 30, 3.90, 25],

            // ===== CHEVILLES =====
            ['Cheville nylon à expansion 6x30mm', 'Chevilles', 'Nylon', '6mm', 30, null, null, 'sachet', 25, 3.90, 55],
            ['Cheville nylon à expansion 8x40mm', 'Chevilles', 'Nylon', '8mm', 40, null, null, 'sachet', 25, 4.90, 50],
            ['Cheville Molly métallique M6', 'Chevilles', 'Acier', 'M6', null, null, null, 'sachet', 10, 7.50, 25],
            ['Cheville béton SX 8x40mm', 'Chevilles', 'Nylon renforcé', '8mm', 40, null, null, 'sachet', 20, 6.20, 25],
            ['Cheville placo autoforeuse 35mm', 'Chevilles', 'Nylon', null, 35, null, null, 'sachet', 10, 6.90, 25],

            // ===== TIGES FILETÉES =====
            ['Tige filetée M6x1000mm', 'Tiges filetées', 'Acier zingué', 'M6', 1000, null, null, 'piece', null, 5.50, 25],
            ['Tige filetée M8x1000mm', 'Tiges filetées', 'Acier zingué', 'M8', 1000, null, null, 'piece', null, 6.90, 25],
            ['Tige filetée M10x1000mm', 'Tiges filetées', 'Acier zingué', 'M10', 1000, null, null, 'piece', null, 8.50, 20],
            ['Tige filetée inox M6x1000mm', 'Tiges filetées', 'Acier inoxydable A2', 'M6', 1000, null, null, 'piece', null, 9.90, 15],
            ['Tige filetée M8x500mm', 'Tiges filetées', 'Acier zingué', 'M8', 500, null, null, 'piece', null, 4.50, 20],

            // ===== RIVETS =====
            ['Rivet aveugle alu 4x10mm', 'Rivets', 'Aluminium', '4mm', 10, null, null, 'boite', 100, 5.90, 30],
            ['Rivet aveugle alu 4.8x16mm', 'Rivets', 'Aluminium', '4.8mm', 16, null, null, 'boite', 100, 6.90, 25],
            ['Rivet inox 4x10mm', 'Rivets', 'Acier inoxydable A2', '4mm', 10, null, null, 'boite', 50, 7.90, 20],
            ['Rivet large tête alu 4.8x16mm', 'Rivets', 'Aluminium', '4.8mm', 16, null, null, 'boite', 50, 7.90, 18],
            ['Rivet plastique/nylon 5x15mm', 'Rivets', 'Nylon', '5mm', 15, null, null, 'boite', 100, 4.20, 20],

            // ===== CLOUS =====
            ['Clou acier tête plate 2.5x50mm', 'Clous', 'Acier', '2.5mm', 50, 'Plate', null, 'kg', 1, 5.90, 45],
            ['Clou galvanisé 3x80mm', 'Clous', 'Acier galvanisé', '3mm', 80, 'Plate', null, 'kg', 1, 6.90, 25],
            ['Clou torsadé 3x70mm', 'Clous', 'Acier', '3mm', 70, 'Plate', null, 'kg', 1, 7.20, 20],
            ['Clou à béton 3x40mm', 'Clous', 'Acier trempé', '3mm', 40, 'Plate', null, 'boite', 100, 6.90, 20],
            ['Clou de tapissier tête bombée 10mm', 'Clous', 'Laiton', '10mm', null, 'Bombée', null, 'boite', 100, 4.90, 25],

            // ===== CHARNIÈRES =====
            ['Charnière plate 60mm inox', 'Charnières', 'Acier inoxydable A2', null, 60, null, null, 'piece', null, 3.90, 40],
            ['Charnière piano 1m', 'Charnières', 'Acier zingué', null, 1000, null, null, 'piece', null, 12.90, 15],
            ['Charnière invisible 100mm', 'Charnières', 'Acier zingué', null, 100, null, null, 'piece', null, 6.50, 25],
            ['Paumelle à visser 100mm', 'Charnières', 'Acier', null, 100, null, null, 'piece', null, 3.50, 30],
            ['Charnière de meuble à clipser 35mm', 'Charnières', 'Acier nickelé', '35mm', null, null, null, 'piece', null, 2.50, 40],

            // ===== SERRURES =====
            ['Serrure en applique 3 points', 'Serrures', 'Acier', null, null, null, null, 'piece', null, 49.90, 10],
            ['Serrure à mortaiser 60mm axe 40mm', 'Serrures', 'Acier', null, 60, null, null, 'piece', null, 29.90, 15],
            ['Cylindre de serrure 30x30mm', 'Serrures', 'Laiton', null, null, null, null, 'piece', null, 19.90, 20],
            ['Cadenas laiton 40mm', 'Serrures', 'Laiton', '40mm', null, null, null, 'piece', null, 11.90, 25],
            ['Verrou à targette 150mm', 'Serrures', 'Acier zingué', null, 150, null, null, 'piece', null, 7.90, 18],

            // ===== ÉQUERRES =====
            ['Équerre plate renforcée 60x60mm', 'Équerres', 'Acier zingué', null, 60, null, null, 'piece', null, 2.50, 80],
            ['Équerre d\'assemblage 100x100mm', 'Équerres', 'Acier zingué', null, 100, null, null, 'piece', null, 4.20, 50],
            ['Équerre invisible 100mm', 'Équerres', 'Acier', null, 100, null, null, 'piece', null, 3.90, 50],
            ['Équerre étagère 200mm', 'Équerres', 'Acier zingué', null, 200, null, null, 'piece', null, 4.90, 35],
            ['Équerre renforcée inox 80mm', 'Équerres', 'Acier inoxydable A2', null, 80, null, null, 'piece', null, 6.50, 22],

            // ===== FIXATIONS =====
            ['Kit de fixation murale universelle', 'Fixations', 'Acier / nylon', null, null, null, null, 'boite', 1, 9.90, 20],
            ['Collier de fixation pour tube 20mm', 'Fixations', 'Acier zingué', '20mm', null, null, null, 'piece', null, 1.50, 100],
            ['Patte de scellement 150mm', 'Fixations', 'Acier zingué', null, 150, null, null, 'piece', null, 3.90, 35],
            ['Kit de fixation TV murale', 'Fixations', 'Acier', null, null, null, null, 'boite', 1, 19.90, 10],
            ['Kit de fixation pour panneau solaire', 'Fixations', 'Acier inoxydable', null, null, null, null, 'boite', 1, 34.90, 8],

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
