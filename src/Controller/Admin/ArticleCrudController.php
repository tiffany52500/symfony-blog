<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleCrudController extends AbstractCrudController
{

    private SluggerInterface $slugger;
    // injection du slugger au niveau du constructeur

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextEditorField::new('contenu'),
            DateTimeField::new('createdAt')->hideOnForm(),
            TextField::new('slug')->hideOnForm()
        ];
    }
    // redéfinir la méthode persistEntity qui va être appelée lors de la création de l'article en BDD
    // générer l'odre INSERT

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // vérifier que $entityInstance est une instance de la classe Article
        if ( !$entityInstance instanceof Article) return;
        $entityInstance->setCreatedAt(new \DateTime());
        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());

        // appel à la méthode héritée afin de persister l'entité
        parent::persistEntity($entityManager, $entityInstance);
    }


}
