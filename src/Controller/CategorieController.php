<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    private CategorieRepository $categorieRepository;
    //demander à symfony d'injecter une instance de ArticleRepository
    //à la création du contrôleur (instance de ArticleRepository)
    /**
     * @param CategorieRepository $categorieRepository
     */
    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        $categories = $this->categorieRepository->findAll();
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categorie/{slug}', name: 'app_categorie_slug')]
    public function slug($slug): Response
    {
        $categorie = $this->categorieRepository->findOneBy(['slug' => $slug]);
        return $this->render('categorie/articles.html.twig', [
            'categorie' => $categorie,
        ]);
    }
}
