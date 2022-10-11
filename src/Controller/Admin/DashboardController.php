<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // génération d'une url pour accéder au crud des articles
        $url = $adminUrlGenerator
            ->setController(ArticleCrudController::class)
            ->generateUrl();
        // rediriger vers cette url
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle("Tiff' Blog");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl("Retour au blog", "fa fa-hand-point-left", $this->generateUrl("app_accueil"));
        yield MenuItem::section();
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section("Articles");
        // crée un sous-menu pour les articles
        yield MenuItem::subMenu("Actions","fa-solid fa-bars")
            ->setSubItems([
                MenuItem::linkToCrud("Liste des articles", "fa fa-eye", Article::class)
                    ->setAction(Crud::PAGE_INDEX)
                    ->setDefaultSort(['createdAt' => 'DESC']),

                MenuItem::linkToCrud("Ajouter article", "fa-regular fa-plus", Article::class)
                            ->setAction(Crud::PAGE_NEW),


            ]);
        yield MenuItem::section("Catégories");
        // crée un sous-menu pour les catégories
        yield MenuItem::subMenu("Catégories","fa-solid fa-bars")
            ->setSubItems([
                MenuItem::linkToCrud("Liste des catégories", "fa fa-eye", Categorie::class)
                    ->setAction(Crud::PAGE_INDEX),

                MenuItem::linkToCrud("Ajouter catégorie", "fa-regular fa-plus", Categorie::class)
                    ->setAction(Crud::PAGE_NEW),

            ]);
    }
}
