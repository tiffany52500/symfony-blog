<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorieCrudController extends AbstractCrudController
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
        return Categorie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextField::new('slug')->hideOnForm(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // vérifier que $entityInstance est une instance de la classe Catégorie
        if ( !$entityInstance instanceof Categorie) return;

        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());

        // appel à la méthode héritée afin de persister l'entité
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle(Crud::PAGE_INDEX, "Liste des catégories");
        $crud->setPageTitle(Crud::PAGE_NEW, "Ajouter une catégorie");
        $crud->setPageTitle(Crud::PAGE_EDIT, "Modifier une catégorie");
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW,
            function (Action $action){
                $action->setLabel("Ajouter une catégorie");
                $action->setIcon("fa fa-square-plus");
                return $action;
            }
        );

        $actions->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
            function (Action $action){
                $action->setLabel("Valider");
                $action->setIcon("fa fa-circle-check");
                return $action;
            }
        );
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);

        return $actions;
    }
}
