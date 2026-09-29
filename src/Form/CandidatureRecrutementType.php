<?php

namespace App\Form;

use App\Entity\CandidatureRecrutement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureRecrutementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lettreMotivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'required' => false,
                'attr' => ['rows' => 5],
            ])
            ->add('cvUrl', TextType::class, [
                'label' => 'Lien vers votre CV',
                'required' => false,
                'attr' => ['placeholder' => 'https://...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CandidatureRecrutement::class]);
    }
}
