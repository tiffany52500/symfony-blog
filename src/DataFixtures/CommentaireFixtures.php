<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Auteur;
use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // initialiser faker
        $faker = Factory::create("fr_FR");

        // commentaires avec auteurs
        for ($i=0;$i<60;$i++) {
            $commentaire = new Commentaire();
            $commentaire->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $commentaire->setContenu($faker->paragraphs(1,true));

            $numArticle = $faker->numberBetween(0,99);
            $commentaire->setArticle($this->getReference("article".$numArticle));

            $numAuteur = $faker->numberBetween(0,29);
            $commentaire->setAuteur($this->getReference("auteur".$numAuteur));

            // générer l'ordre INSERT
            // INSERT INTO article values ("Titre 1", "Contenu de l'article 1")
            $manager->persist($commentaire);
        }

        // commentaires sans auteurs
        for ($i=0;$i<40;$i++) {
            $commentaire = new Commentaire();
            $commentaire->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $commentaire->setContenu($faker->paragraphs(1,true));

            $numArticle = $faker->numberBetween(0,99);
            $commentaire->setArticle($this->getReference("article".$numArticle));

            // générer l'ordre INSERT
            // INSERT INTO article values ("Titre 1", "Contenu de l'article 1")
            $manager->persist($commentaire);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
           AuteurFixtures::class,
            ArticleFixtures::class,
        ];
    }
}
