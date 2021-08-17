<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('text',null,[
                'label'=>'Le nom de la sortie contient',
                'required'=>false,
                'mapped'=>false,
            ])
            ->add('dateDebut',DateTimeType::class,[
                'widget'=>'single_text',
                'required'=>false,
                'mapped'=>false,
            ])
            ->add('dateFin',DateTimeType::class,[
                'widget'=>'single_text',
                'required'=>false,
                'mapped'=>false,
            ])
            ->add('organisateur',CheckboxType::class,['label'=>"Sorties dont je suis l'organisteur/trice"])
            ->add('inscrit',CheckboxType::class,['label'=>"Sorties auquelles je suis inscrit/e"])
            ->add('noninscrit',CheckboxType::class,['label'=>"Sorties auquelles je ne suis pas inscrit/e"])
            ->add('sortiePassee',CheckboxType::class,['label'=>"Sorties passées"])
            /*->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('lieu')
            ->add('etat')
            ->add('campus')
            ->add('users')
            ->add('organisateur')*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
