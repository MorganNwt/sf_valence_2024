<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/admin/articles', name: 'admin.articles')]
class ArticleController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
    ){
    }
    
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('backend/article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        // On créé un nouvel objet Article
        $article = new Article();

        // On crée notre formulaire en lui passant l'objet qu'il doit remplir
        $form = $this->createForm(ArticleType::class, $article);

        // On passe la request au formulaire pour qu'il puisse récupérer les données
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on persiste l'objet en base de données
        if($form->isSubmitted() && $form->isValid() ){
            // $categorie->setCreatedAt( new \DateTimeImmutable() );

            // On met en file d'attente l'objet à persister
            $this->em->persist($article);

            // On exécute la file d'attente
            $this->em->flush();

            // On créé un message flash pour informer l'utilisateur que la catégorie a bien été crée
            $this->addFlash('success', 'L\'article a bien été créee');

            return $this->redirectToRoute('admin.articles.index');
        }

        return $this->render('backend/article/create.html.twig', [
           'form' => $form
        ]);
    }
}
