<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\RechercheSortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
            ->add('text',SearchType::class,[
                'required'=>false,
                'label'=>'Le nom de la sortie contient',
            ])
            ->add('dateDebut',DateTimeType::class,[
                'widget'=>'single_text',
                'label'=>'Entre',
                'required'=>false,
                'empty_data' => '',
            ])
            ->add('dateFin',DateTimeType::class,[
                'widget'=>'single_text',
                'label'=>'et',
                'required'=>false,
                'empty_data' => '',
            ])
            ->add('organisateur',CheckboxType::class,[
                'label'=>"Sorties dont je suis l'organisteur/trice",
                'required'=>false,

            ])
            ->add('inscrit',CheckboxType::class,[
                'label'=>"Sorties auquelles je suis inscrit/e",
                'required'=>false,
            ])
            ->add('nonInscrit',CheckboxType::class,[
                'label'=>"Sorties auquelles je ne suis pas inscrit/e",
                'required'=>false,
            ])
            ->add('sortiesPassees',CheckboxType::class,[
                'label'=>"Sorties passées",
                'required'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RechercheSortie::class,
        ]);
    }
}
