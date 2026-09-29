<?php

namespace App\Form;

use App\Entity\ContributionProjetInterne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContributionProjetInterneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextareaType::class, [
            'label' => 'Message (motivation, disponibilité, compétences...)',
            'required' => false,
            'attr' => ['rows' => 4, 'placeholder' => 'Présentez-vous et expliquez pourquoi vous souhaitez contribuer à ce projet.'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ContributionProjetInterne::class]);
    }
}
