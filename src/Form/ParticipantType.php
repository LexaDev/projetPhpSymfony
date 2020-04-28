<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',TextType::class,[
            'label'=>'Pseudo',
            'required'=>true
            ])
            ->add('firstName',TextType::class,[
                'label'=>'Prénom',
                'required'=>true
             ])
            ->add('lastName',TextType::class,[
                'label'=>'Nom',
                'required'=>true
            ])
            ->add('phoneNumber', TelType::class,[
                'label'=>'Téléphone',
                'attr'=>[
                    'pattern'=>'#^(\d{2}\s*){5}$#'
                        ]
            ])
            ->add('email',EmailType::class,[
                'label'=>'E-mail',
                'required'=>true,
                 'attr'=>[
                     'pattern'=>'#^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$#'
                        ]
             ])
            ->add('site',EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>'Site',
                'expanded'=>true
            ])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de Passe'],
                'second_options' => ['label' => 'Saisir à nouveau'],
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
