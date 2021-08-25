<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImportCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fichier',FileType::class,[
                'label'=>'Fichier en format .csv',
                'constraints'=>[
                    new File([
                        'maxSize'=>'5000k',
                        //'mimeTypes' => ['application/csv', 'application/x-csv','application/vnd.ms-excel'],
                        //'mimeTypesMessage'=>'Le fichier doit être au format .csv'
                    ])
                ]
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
