<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // initialiser faker
        $faker = Factory::create("fr_FR");

        for ($i=0;$i<30;$i++){
            $auteur = new Auteur();
            $auteur->setPrenom($faker->firstName);
            $auteur->setNom($faker->lastName);
            // création du pseudo avec la première lettre du prénom et le nom de famille.
            $pseudo = strtolower($auteur->getPrenom()[0].".".$auteur->getNom());
            $auteur->setPseudo($pseudo);


            // générer l'ordre INSERT
            // INSERT INTO article values ("Titre 1", "Contenu de l'article 1")
            $manager->persist($auteur);

            // créer une référence sur auteur
            $this->addReference("auteur".$i,$auteur);
        }

        $manager->flush();
    }
}
