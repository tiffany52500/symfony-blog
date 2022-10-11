<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
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
    private CommentaireRepository $commentaireRepository;
    //demander à symfony d'injecter une instance de ArticleRepository
    //à la création du contrôleur (instance de ArticleRepository)
    /**
     * @param ArticleRepository $articleRepository
     * @param CommentaireRepository $commentaireRepository
     */
    public function __construct(ArticleRepository $articleRepository, CommentaireRepository $commentaireRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->commentaireRepository = $commentaireRepository;
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
            $this->articleRepository->findBy([], ["createdAt" => "DESC", "estPublie" => true]),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('article/index.html.twig', [
            "articles" => $articles,
        ]);

    }

    #[Route('/articles/{slug}', name: 'app_article_slug')]
    public function detail($slug, Request $request): Response
    {
        $commentaire = new Commentaire();
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);

        //reconnaître si le formulaire a été soumis ou pas
        $formCommentaire->handleRequest($request);
        //est-ce que le formulaire a été soumis ?
        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            $commentaire->setCreatedAt(new \DateTime());
            $this->commentaireRepository->add($commentaire,true);
            return $this->redirectToRoute('app_article_slug', ['slug' => $slug]);
        }


        $article = $this->articleRepository->findOneBy(['slug' => $slug]);

        return $this->renderForm('article/detail.html.twig', [
            'formCommentaire'=>$formCommentaire,
            'article' => $article
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

    #[Route('/articles/edit/{slug}', name: 'app_articles_edit', methods: ['GET', 'POST'], priority: 1)]
    public function edit($slug, Request $request) : Response {
        $article = $this->articleRepository->findOneBy(['slug' => $slug]);
        //création du formulaire
        $formArticle = $this->createForm(ArticleType::class, $article);

        //reconnaître si le formulaire a été soumis ou pas
        $formArticle->handleRequest($request);
        //est-ce que le formulaire a été soumis ?
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $this->articleRepository->add($article,true);
            return $this->redirectToRoute('app_article_slug', ['slug' => $slug]);
        }

        //appel de la vue twig permettant d'afficher le formulaire
        return $this->renderForm('article/edit.html.twig', [
            'formArticle'=>$formArticle
        ]);

    }

}
