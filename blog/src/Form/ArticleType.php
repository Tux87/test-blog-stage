<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required'   => true,
                "label" => "Titre :",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('content', TextareaType::class, [
                'required'   => true,
                "label" => "Content :",
                'empty_data' => '',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('cover', FileType::class, [
                'required'   => true,
                'data_class' => null,
                "label" => "Image :",
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('date_created')            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
