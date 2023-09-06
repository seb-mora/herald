<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Messages;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Security;

class MessagesType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder,  array $options): void
    {

        $user = $this->security->getUser();

        $builder
            ->add('sujet', null, [
                'label' => 'Sujet : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96'
                ],
            ])
            ->add('contenu', null, [
                'label' => 'Message : ',
                'attr' => [
                    'class' => 'input input-bordered border-emerald-600 input-accent w-96',
                    'style' => 'height: 200px; width: 75%'
                ],
            ])
            ->add('fk_destinataire', EntityType::class, [
                'class' => Users::class,
                'choice_label' => 'login',
                'label' => 'Destinataire : ',
                'required' => 'true',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('user')
                        ->where('user.id != :currentUserId')
                        ->setParameter('currentUserId', $user->getId());
                },
                'placeholder' => 'Choisissez un destinataire',
                'attr' => [
                    'class' => 'select select-border border-emerald-600 ',
                    'style' => 'border: 1px solid #059669; border-radius: 5px;'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
