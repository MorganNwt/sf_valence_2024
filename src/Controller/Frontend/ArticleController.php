<?php

namespace App\Controller\Frontend;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/articles', name: 'app.articles')]
class ArticleController extends AbstractController
{
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('frontend/article/index.html.twig', [
            'articles' => $articleRepository->findBy(['enable'=> true], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: '.show', methods: ['GET'])]
    public function show(?Article $article): Response|RedirectResponse // show = page de detail
    {
        if(!$article || !$article->isEnable()){
            $this->addFlash('error', 'Article non trouvé');

            return $this->redirectToRoute('app.articles.index');
        }

        return $this->render('frontend/article/show.html.twig', [
            'article'=> $article,
        ]);
    }
}
