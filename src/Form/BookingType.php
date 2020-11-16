<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('loaningDate', DateTimeType::class, [
                'data' => new \DateTime()
            ])
            ->add('returnDate', DateTimeType::class, [
                'data' => new \DateTime()
            ])
            ->add('status')
            // ->add('user')
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
            'data_class' => Booking::class,
            'validation_groups' => false,
        ]);
    }
}
