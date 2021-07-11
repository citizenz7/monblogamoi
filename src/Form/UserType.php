<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('nick', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            //->add('roles')
            //->add('password')
            //->add('isVerified')
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'class' => 'form-control mb-3'
                ]
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Avatar de votre compte',
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg'
                        ],
                        'maxSizeMessage' => 'L\'image est trop grosse (2 Mo maxi)',
                        'mimeTypesMessage' => 'L\'image n\'est pas valide (jpeg et png seulement)',
                    ])
                ]
            ])
            //->add('created_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
