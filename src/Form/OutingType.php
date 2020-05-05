<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Outing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class OutingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie :',
                'required' => true,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                        ]
            ])
            ->add('dateTimeStart', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'required' => true,
                'widget' => 'single_text',
                'html5' => true,
                'attr'=>[
                    'class'=>'ml-0 form-control',
                    'type'=>"datetime-local"
                        ]
            ])
            ->add('dateLimitSigningUp', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'required' => true,
                'widget' => 'single_text',
                'html5' => true,
                'attr'=>[
                'class'=>'ml-0 form-control',
                'type'=>"date"
                    ]
            ])
            ->add('nbSigningUpMax', TextType::class, [
                'label' => 'Nombre de places :',
                'required' => true,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]
            ])
            ->add('duration', ChoiceType::class, [
                'label' => 'Durée :',
                'required' => true,
                'choices' => [
                    '45 minutes' => '45',
                    '1 heure' => '60',
                    '1 heure et demi' => '90',
                    '2 heures' => '120',
                    '3 heures' => '180',
                ],
                'attr'=>[
                     'class'=>'ml-0 form-control'
    ]
            ])
            ->add('infosOuting', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => false,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                ]
            ])
            ->add('location', EntityType::class,array(
                'class' => Location::class,
                'label'=> 'Lieu :',
                'choice_label' => 'name',
                'placeholder' => 'Choisir un lieu..',
                'multiple' => false,
                'expanded' => false,
                'attr'=>[
                    'class'=>'ml-0 form-control'
                    ]
            ));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outing::class,
        ]);
    }
}
