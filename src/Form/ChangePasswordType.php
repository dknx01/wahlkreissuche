<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'oldPassword',
            PasswordType::class,
            [
                'help' => 'oldPassword',
            ]
        )
            ->add('newPassword1', PasswordType::class)
            ->add('newPassword2', PasswordType::class)
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'save',
                    'translation_domain' => 'generic',
                    'attr' => [
                        'class' => 'btn btn-partei',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'user',
        ]);
    }
}
