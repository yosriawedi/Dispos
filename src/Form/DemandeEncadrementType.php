<?php

namespace App\Form;

use App\Entity\DemandeEncadrement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DemandeEncadrementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type de projet',
                'choices' => [
                    'PFE' => DemandeEncadrement::TYPE_PFE,
                    'PFA' => DemandeEncadrement::TYPE_PFA,
                    'Doctorat' => DemandeEncadrement::TYPE_DOCTORAT,
                ],
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet visé',
                'constraints' => [new NotBlank(['message' => 'Le sujet est requis'])],
                'attr' => ['placeholder' => 'Ex: Plateforme de recommandation IA pour e-commerce'],
            ])
            ->add('stackTech', TextareaType::class, [
                'label' => 'Stack technique souhaitée',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Symfony, React, PostgreSQL...', 'rows' => 3],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description complémentaire',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('etablissement', TextType::class, [
                'label' => 'Établissement',
                'required' => false,
            ])
            ->add('niveauEtude', ChoiceType::class, [
                'label' => 'Niveau d\'étude',
                'required' => false,
                'placeholder' => 'Sélectionnez votre niveau',
                'choices' => [
                    'PFE Licence' => 'PFE Licence',
                    'PFE Master' => 'PFE Master',
                    'PFE Ingénieur' => 'PFE Ingénieur',
                    'PFA' => 'PFA',
                    'Stage Technicien' => 'Stage Technicien',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => DemandeEncadrement::class]);
    }
}
