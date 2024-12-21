<?php

namespace App\Form;

use App\Entity\ArticleCommande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $articleRepository = $options['article_repository'];

        $builder
            ->add('article', ChoiceType::class, [
                'choices' => $articleRepository->findAll(),
                'choice_label' => function ($article) {
                    return $article->getNom();
                },
                'placeholder' => 'Choisir un article',
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleCommande::class,
        ]);

        $resolver->setRequired('article_repository');
    }
}
