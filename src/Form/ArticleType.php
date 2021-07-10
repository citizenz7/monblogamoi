<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('presentation', TextType::class, [
                'label' => 'Présentation courte 200 caractères (chapeau)',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'rows' => '4'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu : texte de l\'article',
                'attr' => [
                    'class="form-control mb-3',
                    'rows' => '8'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'article',
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Image is not valid',
                    ])
                ]
            ])
            //->add('created_at')
            //->add('updated_at')
            //->add('author')
            ->add('category', EntityType::class, [
                'label' => 'Catégories de l\'article. Choisissez-en une ou plusieurs.',
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.title', 'ASC');
                }
            ])
            ->add('tag', EntityType::class, [
                'label' => 'Tags de l\'article. Vous pouvez choisir les tags dans la liste ou en créer de nouveaux simplement en tapant leur nom suivi dun espace.',
                'class' => Tag::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control mb-3 select-tags'
                ],
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.title', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
