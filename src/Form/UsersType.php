<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', null, [
                'label' => 'Login : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Employé' => "ROLE_REGISTERED",
                    'Admin' => "ROLE_ADMIN",
                ],
                'label' => 'Rôle : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                ],
                'required' => is_null($builder->getData()->getId()) ? true : false,
            ])



            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Mot de passe',
                'required' => false,
                // 'mapped' => false,
                'first_options' => ['label' => 'Mot de passe : ', 'attr' => [
                    'class' => 'password-field input input-bordered border-emerald-600 input-accent w-96',
                    'autocomplete' => 'new-password'
                ]],
                'second_options' => ['label' => 'Confirmation du mot de passe : ', 'attr' => [
                    'class' => 'password-field input input-bordered border-emerald-600 input-accent w-96',
                    'autocomplete' => 'new-password'
                ]],
                'empty_data' => '',
            ])

            ->add('nom', null, [
                'label' => 'Nom : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('prenom', null, [
                'label' => 'Prénom : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('adresse', null, [
                'label' => 'Adresse : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('telephone', null, [
                'label' => 'Téléphone : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('date_in', DateTimeType::class, [
                'label' => "Date d'embauche : ",
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                ],
            ])
            ->add('present', null, [
                'label' => 'Présent : ',
                'attr' => [
                    'checked' => 'checked',
                    'class' => 'checkbox checkbox-secondary'
                ],
                'label_attr' => [
                    'class' => 'text-3xl',
                ],
            ])
            // ->add('date_out')
            ->add('fk_equipe', null, [
                'label' => 'Equipe : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('fk_status', null, [
                'label' => 'Status : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ]);

        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
