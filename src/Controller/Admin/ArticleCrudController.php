<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
            TextEditorField::new('contenu')->hideOnIndex()
                                                        ->setSortable(false),
            AssociationField::new('categorie')->setRequired(false),
            DateTimeField::new('createdAt', 'Date de création')->hideOnForm(),
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

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPageTitle(Crud::PAGE_INDEX, "Liste des articles");
        $crud->setPageTitle(Crud::PAGE_NEW, "Ajouter un article");
        $crud->setPageTitle(Crud::PAGE_EDIT, "Modifier un article");
        $crud->setPaginatorPageSize(10);
        $crud->setDefaultSort(['createdAt' => 'DESC']);
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW,
            function (Action $action){
                $action->setLabel("Ajouter un article");
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
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add("titre");
        $filters->add("createdAt");
        return $filters;
    }




}
