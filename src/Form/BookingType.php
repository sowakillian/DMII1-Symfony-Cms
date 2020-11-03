<?php

namespace App\Form;

use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Booking;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('loaningDate', DateTimeType::class, [
                'label_translation_parameters' => [
                    'Loaning date' => $this->translator->trans('Loaning date', [], 'booking'),
                    'Year' => $this->translator->trans('Year', [], 'date'),
                    'Month' => $this->translator->trans('Month', [], 'date'),
                    'Day' => $this->translator->trans('Day', [], 'date'),
                    'Hour' => $this->translator->trans('Hour', [], 'date'),
                    'Minute' => $this->translator->trans('Minute', [], 'date'),
                ],
            ])
            ->add('returnDate', DateTimeType::class, [
                'label_translation_parameters' => [
                    'Return date' => $this->translator->trans('Return date', [], 'booking'),
                    'Year' => $this->translator->trans('Year', [], 'date'),
                    'Month' => $this->translator->trans('Month', [], 'date'),
                    'Day' => $this->translator->trans('Day', [], 'date'),
                    'Hour' => $this->translator->trans('Hour', [], 'date'),
                    'Minute' => $this->translator->trans('Minute', [], 'date'),
                ],
            ])
            ->add('status', TextType::class, [
                'label_translation_parameters' => [
                    'Status' => $this->translator->trans('Status', [], 'booking'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
