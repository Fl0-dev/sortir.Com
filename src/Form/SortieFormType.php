<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use phpDocumentor\Reflection\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget'=>'single_text'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'widget'=>'single_text'
            ])
            ->add('duree')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            ->add('etat', EntityType::class,[
                'class'=>Etat::class,
                'choice_label'=>'libelle'
            ])
            ->add('campus',EntityType::class,[
                'class'=>Campus::class,
                'choice_label'=>'nom'])
            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'choice_label'=>'nom',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
