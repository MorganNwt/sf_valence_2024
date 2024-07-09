<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\ORM\QueryBuilder;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Nom de l\'article'
                ],
                'required' => true,
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Categories',
                'class' => Categorie::class,
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => true,
                'autocomplete' => true,
                'query_builder' => function (CategorieRepository $repo): QueryBuilder{
                    return $repo->createQueryBuilder('c' )
                        ->andWhere('c.enable = true')
                        ->orderBy('c.name', 'ASC');
                },
            ])
            ->add('content' , TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'placeholder' => 'Contenu de l\'article',
                    'rows' => 10,
                ],
                'required' => true,
            ])
            ->add('enable', CheckboxType::class,[
                'label' => 'Actif',
                'required' => 'false',
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
