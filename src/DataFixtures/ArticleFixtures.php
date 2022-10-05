<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;
    //demander à symfony d'injecter le slugger au niveau du constructeur

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager): void
    {
        // initialiser faker
        $faker = Factory::create("fr_FR");

        for ($i=0;$i<100;$i++){
            $article = new Article();
            $article->setTitre($faker->words($faker->numberBetween(3,10), true));
            $article->setContenu($faker->paragraphs(3,true));
            $article->setCreatedAt($faker->dateTimeBetween('-6 months'));
            $article->setSlug($this->slugger->slug($article->getTitre())->lower());

            // associer l'article à une catégorie
            // récupérer une référence d'une catégorie
            $numCategorie = $faker->numberBetween(0,8);
            $article->setCategorie($this->getReference("categorie".$numCategorie));

            // générer l'ordre INSERT
            // INSERT INTO article values ("Titre 1", "Contenu de l'article 1")
            $manager->persist($article);

            // créer une référence sur article
            $this->addReference("article".$i,$article);

        }

        // envoyer l'ordre INSERT vers la base
        $manager->flush();

    }

    public function getDependencies()
    {
        return [
          CategorieFixtures::class
        ];
    }
}
