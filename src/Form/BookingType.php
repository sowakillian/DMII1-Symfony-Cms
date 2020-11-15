<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Equipement;
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
            ->add('equipements', EntityType::class, [
                'class' => Equipement::class,
                'multiple' => true,
                'choice_label' => function ($equipement) {
                    return $equipement->getName();
                },
                'choice_attr' => function ($equipement) {
                    return ['data-category' => $equipement->getCategory()->getId()];
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
