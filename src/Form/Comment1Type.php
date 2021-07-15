<?php

namespace App\Form;

use App\Entity\Comment;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Comment1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Texte du commentaire',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('active', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'attr' => [
                    'class' => 'mb-3'
                ]
            ])
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            /*
            ->add('created_at', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'label' => 'Date d\'inscription',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            */
            ->add('rgpd', CheckboxType::class, [
                'label' => 'RGPD',
                'attr' => [
                    'class' => 'mb-3 mx-2'
                ]
            ])
            //->add('article')
            //->add('parent')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
