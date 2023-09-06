<?php

namespace App\Form;

use App\Entity\Chantiers;
use Symfony\Component\Form\AbstractType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ChantiersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('localisation', null, [
                'label' => 'Localisation : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('fk_client', null, [
                'label' => 'Client : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('date_debut', DateTimeType::class, [
                'label' => "Début du chantier : ",
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                ],
            ])
            ->add('date_fin', DateTimeType::class, [
                'label' => "Fin du chantier : ",
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                ],
            ])
            ->add('duree_sem', null, [
                'label' => 'Durée (en semaines) : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('montant', null, [
                'label' => 'Montant : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('facture_emise', null, [
                'label' => 'Facture émise ? ',
                'attr' => [
                    'class' => 'icheckbox checkbox-secondary'
                ],
            ])
            ->add('paiement_recu', null, [
                'label' => 'Paiement reçu ? ',
                'attr' => [
                    'class' => 'checkbox checkbox-secondary'
                ],
            ])
            ->add('retour_client', null, [
                'label' => 'Retour client : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('retour_equipe', null, [
                'label' => 'Retour équipe : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('clos', null, [
                'label' => 'Affaire close ? ',
                'attr' => [
                    'class' => 'checkbox checkbox-secondary'
                ],
            ])
            ->add('Description', null, [
                'label' => 'Description : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('photo', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Lien de photo : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('descrphotos', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Légende photo : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('principale', CheckboxType::class, [
                'label' => 'Photo principale ? ',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'checkbox checkbox-secondary'
                ],
                'label_attr' => [
                    'class' => 'text-3xl',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chantiers::class,
        ]);
    }
}
