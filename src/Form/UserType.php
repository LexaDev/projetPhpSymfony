<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
                'label'=>'Pseudo',
                'required'=>true,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]
            ])
            ->add('firstName',TextType::class,[
                'label'=>'Prénom',
                'required'=>true,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]
            ])
            ->add('lastName',TextType::class,[
                'label'=>'Nom',
                'required'=> true,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]
            ])
            ->add('phoneNumber', TelType::class,[
                'label'=>'Téléphone',
                'required'=>false,
                'attr'=>[
                    'class'=>'ml-0 form-control',

                ]

            ])
            ->add('email',EmailType::class,[
                'label'=>'E-mail',
                'required'=>true,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]
            ])
            ->add('site',EntityType::class,[
                'class'=>Site::class,
                'label'=>'Site',
                'choice_label'=>'name',
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]

            ])
            ->add('newPassword',RepeatedType::class,[
                'type' =>PasswordType::class,
                'required'=>true,
                'mapped'=>false,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => [
                    'label' => 'Mot de Passe',
                    'mapped'=>false,
                    'attr'=>[
                        'class'=>'ml-0 form-control'
                    ]
                ],
                'second_options' => [
                    'label' => 'Saisir à  nouveau',
                    'mapped'=>false,
                    'attr'=>[
                        'class'=>'ml-0 form-control'
                    ]
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPEG, JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                                 'maxSize' => '1024k',
                                 'mimeTypes' => [
                                     'image/jpeg',
                                     'image/jpg',
                                     'image/png',
                                 ],
                                 'mimeTypesMessage' => 'Merci de télécharger une image valide',
                             ])
                ]
            ])
            ->add('actif',CheckboxType::class,[
                'label'=>'Activer le participant',
                  'required'=>false,
                  'label_attr'=>[
                      'class'=>'custom-control-label',
                      'required'=>false
                  ],
                  'attr'=>[
                      'class'=>'custom-control-input'
                  ]
                         ]
            )
            ->add('roles',ChoiceType::class,[
                'mapped'=>false,
                'choices'=>[
                    'User'=>'0',
                    'Admin'=>'1',
                ],
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
