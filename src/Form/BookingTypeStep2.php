<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingTypeStep2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('equipments', EntityType::class, [
                'class' => Equipment::class,
                'multiple' => true,
                'choice_label' => function ($equipment) {
                    return $equipment->getName();
                },
                'choice_attr' => function ($equipment) {
                    return ['data-category' => $equipment->getCategory()->getId()];
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class
        ]);
    }
}
