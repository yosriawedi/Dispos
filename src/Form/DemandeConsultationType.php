<?php

namespace App\Form;

use App\Entity\DemandeConsultation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DemandeConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextareaType::class, [
            'label' => 'Votre demande',
            'constraints' => [new NotBlank(['message' => 'Merci de préciser votre demande'])],
            'attr' => ['rows' => 4, 'placeholder' => 'Expliquez ce que vous souhaitez faire réviser ou discuter avec l\'équipe DisPos.'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DemandeConsultation::class]);
    }
}
