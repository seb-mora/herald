<?php

namespace App\Form;

use App\Entity\InfoUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class InfoUserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_show', DateTimeType::class, [
                'label' => "Date d'apparition : ",
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                ],
            ])
            ->add('date_hide', DateTimeType::class, [
                'required' => false,
                'label' => 'Date de retrait : ',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoUser::class,
        ]);
    }
}
