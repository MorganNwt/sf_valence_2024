<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[route('/admin/categories', name: 'admin.categories')]
class CategorieController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em,
        ){
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(CategorieRepository $repo): Response
    {
         //$categories = $repo->findAll();
        //dd($categories);

        return $this->render('backend\categorie\index.html.twig', [
            'categories' => $repo->findall(),
        ]);
    }


    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        // On créé un nouvel objet Categorie
        $categorie = new Categorie();

        // On crée notre formulaire en lui passant l'objet qu'il doit remplir
        $form = $this->createForm(CategorieType::class, $categorie);

        // On passe la request au formulaire pour qu'il puisse récupérer les données
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on persiste l'objet en base de données
        if($form->isSubmitted() && $form->isValid() ){
            // $categorie->setCreatedAt( new \DateTimeImmutable() );

            // On met en file d'attente l'objet à persister
            $this->em->persist($categorie);

            // On exécute la file d'attente
            $this->em->flush();

            // On créé un message flash pour informer l'utilisateur que la catégorie a bien été crée
            $this->addFlash('sucess', 'La catégorie a bien été créee');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('backend/categorie/create.html.twig', [
           'form' => $form
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    public function update(?Categorie $categorie, Request $request): Response
    {
       if(!$categorie){
        $this->addFlash('error', 'La catégorie demandé n\'éxiste pas');

        return $this->redirectToRoute('admin.categories.index');
       }

       $form = $this->createForm(CategorieType::class, $categorie);
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){

            $this->em->persist($categorie);
            $this->em->flush();

            $this->addFlash('success', 'La catégorie a bien étét modifiée');

            return $this->redirectToRoute('admin.categories.index');
       }

       return $this->render('backend/categorie/update.html.twig',[
        'form' => $form,
       ]);
    }

    #[route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Categorie $categorie, Request $request): RedirectResponse
    {
        if(!$categorie){
            $this->addFlash('error', 'La catégorie demandé n\'existe pas');

            return $this->redirectToRoute('admin.categories.index');
        }

        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('token'))) {
            $this->em->remove($categorie);
             $this->em->flush();

             $this->addFlash('sucess', 'La catégorie a bien été suprimée');    
        }else {
            $this->addFlash('error', 'Le jeton CSRF est invalide');
        }

        return $this->redirectToRoute('admin.categories.index');
        
    }
}
