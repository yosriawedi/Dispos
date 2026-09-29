<?php

namespace App\Form;

use App\Entity\DemandeReduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DemandeReductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('contextePrestationCiblee', TextType::class, [
            'label' => 'Prestation DisPos visée par la réduction',
            'required' => false,
            'attr' => ['placeholder' => 'Ex: incubation PFE, session de révision Algorithmique...'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DemandeReduction::class]);
    }
}
