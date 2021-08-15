<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];

        $builder->add('title', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'trim' => true,
            'error_bubbling' => false,
            'empty_data' => '',
            'constraints' => [
                new Length([
                    'maxMessage' => 'Title cannot be longer than {{ limit }} characters',
                    'max' => 40,
                ]),
                new NotBlank([
                    'message' => 'Title cannot be null',
                ])
            ],
        ]);
        $builder->add('shortDescription', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'trim' => true,
            'error_bubbling' => false,
            'empty_data' => '',
            'constraints' => [
                new Length([
                    'maxMessage' => 'Short Description cannot be longer than {{ limit }} characters',
                    'max' => 50,
                ]),
                new NotBlank([
                    'message' => 'Short description cannot be blank',
                ])
            ]
        ]);
        $builder->add('body', TextareaType::class, [
            'attr' => ['class' => 'form-control'],
            'trim' => true,
            'error_bubbling' => false,
            'empty_data' => '',
            'constraints' => [
                new NotBlank([
                    'message' => 'Body cannot be blank',
                ])
            ]
        ]);
        $builder->add('imageFile', FileType::class, [
            'attr'     => ['class' => 'form-control'],
            'mapped' => false,
            'required' => false,
            'error_bubbling' => false,
            'constraints' => $imageConstraints,
        ]);
        $builder->add('is_published', CheckboxType::class, [
            'attr' => ['class' => 'form-check-input d-inline-block mx-2'],
            'error_bubbling' => false,
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
