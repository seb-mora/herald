<?php

namespace App\Form;

use App\Entity\Clients;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientsType extends AbstractType
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Clients::class,
        ]);
    }
}
