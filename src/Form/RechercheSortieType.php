<?php

namespace App\Form;

use App\Entity\Campus;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom',

            ])
            ->add('text',TextType::class,[
                'label'=>'Le nom de la sortie contient',
                'required'=>false,
            ])
            ->add('dateHeureDebut',DateTimeType::class,[
                'widget'=>'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
