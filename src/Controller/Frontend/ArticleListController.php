<?php

namespace App\Controller\Frontend;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleListController extends AbstractController
{
    #[Route('/article_list', name: 'app.home.article_list', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('frontend/article_list/index.html.twig', [
            'articles' => $articleRepository->findLatestArticle(3),
        ]);
    }
}
