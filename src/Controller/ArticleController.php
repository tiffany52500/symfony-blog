<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Container3IT5QDv\PaginatorInterface_82dac15;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;
    //demander à symfony d'injecter une instance de ArticleRepository
    //à la création du contrôleur (instance de ArticleRepository)

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route('/articles', name: 'app_articles')]
    // a l'appel de la méthode, symfony va crée un objet de la classe ArticleRepository et le passer en paramètre de la méthode
    // ce mécanisme s'appelle "INJECTION DE DEPENDANCES"
    public function getArticles(PaginatorInterface $paginator, Request $request): Response
    {
        // récupérer les informations dans la base de données
        // le controleur fait appel au modèle (une classe du modèle) afin de récupérer la liste des articles
        // $repository = new ArticleRepository();

        // mise en place de la pagination

        $articles = $paginator->paginate(
            $this->articleRepository->findBy([], ["createdAt" => "DESC"]),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('article/index.html.twig', [
            "articles" => $articles,
        ]);

    }

    #[Route('/articles/{slug}', name: 'app_article_slug')]
    public function detail($slug): Response
    {

        $article = $this->articleRepository->findOneBy(['slug' => $slug]);
        return $this->render('article/detail.html.twig', [
            "article" => $article
        ]);
    }

    #[Route('/articles/nouveau', name: 'app_articles_nouveau', methods: ['GET', 'POST'], priority: 1)]
    public function insert(SluggerInterface $slugger, Request $request) : Response {
        $article = new Article();
        //création du formulaire
        $formArticle = $this->createForm(ArticleType::class, $article);

        //reconnaître si le formulaire a été soumis ou pas
        $formArticle->handleRequest($request);
        //est-ce que le formulaire a été soumis ?
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $article->setSlug($slugger->slug($article->getTitre())->lower())
                    ->setCreatedAt(new \DateTime());
            $this->articleRepository->add($article,true);
            return $this->redirectToRoute('app_articles');
        }

        //appel de la vue twig permettant d'afficher le formulaire
        return $this->renderForm('article/nouveau.html.twig', [
            'formArticle'=>$formArticle
        ]);



        /* $article->setTitre('Nouvel article 2')
                ->setContenu("Contenu du nouvel article 2")
                ->setSlug($slugger->slug($article->getTitre())->lower())
                ->setCreatedAt(new \DateTime());
        $this->articleRepository->add($article, true);
        return $this->redirectToRoute("app_articles");
        */

    }

}
