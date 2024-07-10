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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;


#[Route('/admin/articles', name: 'admin.articles')]
class ArticleController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
    ){
    }
    
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('backend/article/index.html.twig', [
            'articles' => $articleRepository->findall(),
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

            // pour récupérer automatiquement l'utilisateur connecté pour la relation ManyToOne
            $article->setUser($this->getUser()); 
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

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Article $article, Request $request): Response|RedirectResponse
    {
       if(!$article){
        $this->addFlash('error', 'L\'article demandé n\'éxiste pas');

        return $this->redirectToRoute('admin.articles.index');
       }

       $form = $this->createForm(ArticleType::class, $article);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', 'L\'article a bien été modifiée');

            return $this->redirectToRoute('admin.articles.index');
       }

       return $this->render('backend/article/update.html.twig',[
        'form' => $form,
       ]);
    }

    #[route('/{id}/delete', name: '.delete', methods: ['POST'] )]
    public function delete(?Article $article, Request $request): RedirectResponse
    {
        if(!$article){
            $this->addFlash('error', 'L\'article demandé n\'existe pas');

            return $this->redirectToRoute('admin.articles.index');
        }
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('token'))) {
            $this->em->remove($article);
             $this->em->flush();

             $this->addFlash('sucess', 'L\'article a bien été suprimée');    
        }else {
            $this->addFlash('error', 'Le jeton CSRF est invalide');
        }

        return $this->redirectToRoute('admin.articles.index');
    }

    #[route('/{id}/switch', name: '.switch', methods: ['GET'])]
    public function switch(?Article $article): JsonResponse
    {
        if (!$article){
            return $this->json([
                'status'=>'error',
                'message'=>'Article non trouvé',
            ], 404);
        }

        $article->setEnable(!$article->isEnable());

        $this->em->persist($article);
        $this->em->flush();

        return $this->json([
            'status'=>'success',
            'message'=>'Article modifié avec succès',
            'enable'=> $article->isEnable(),
        ]);
    }
}
