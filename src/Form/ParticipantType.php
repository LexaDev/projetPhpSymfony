<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
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
            'attr'=>[
                'placeholder'=>''
            ]

            ])
            ->add('roles')
            ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('phoneNumber')
            ->add('email')
            ->add('actif')
            ->add('site')
            ->add('outingsParticipate')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
