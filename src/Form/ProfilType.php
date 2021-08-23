<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('email')
            ->add('new_password', RepeatedType::class, [
                'required'=>false,
                'type' => PasswordType::class,
                "mapped"=>false,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmation'],
            ])
            ->add('plainPassword', PasswordType::class,["mapped"=>false, 'label' => 'Mot de passe'])
            ->add('campus', null, ["choice_label"=>"nom"])
            ->add('photo',FileType::class,[
                'mapped'=>false,
                'required'=>false,
                'label'=>'Ma photo (format .jpg)',
                'constraints'=>[
                    new File([
                       'maxSize'=>'5000k',
                        'mimeTypesMessage'=>'On veux une photo en .jpg'
                    ])
                ]
            ])
            //->add('roles')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
