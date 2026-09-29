<?php

namespace App\Form;

use App\Entity\OffreRecrutement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OffreRecrutementType extends AbstractType
{
    public const GOUVERNORATS = [
        'Ariana', 'Béja', 'Ben Arous', 'Bizerte', 'Gabès', 'Gafsa',
        'Jendouba', 'Kairouan', 'Kasserine', 'Kébili', 'Le Kef', 'Mahdia',
        'La Manouba', 'Médenine', 'Monastir', 'Nabeul', 'Sfax', 'Sidi Bouzid',
        'Siliana', 'Sousse', 'Tataouine', 'Tozeur', 'Tunis', 'Zaghouan',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('poste', TextType::class, [
                'label' => 'Intitulé du poste',
                'constraints' => [new NotBlank(['message' => 'Le poste est requis'])],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du poste',
                'constraints' => [new NotBlank(['message' => 'La description est requise'])],
                'attr' => ['rows' => 5],
            ])
            ->add('typeContrat', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'CDI' => OffreRecrutement::TYPE_CDI,
                    'CDD' => OffreRecrutement::TYPE_CDD,
                    'Stage' => OffreRecrutement::TYPE_STAGE,
                    'Freelance' => OffreRecrutement::TYPE_FREELANCE,
                    'Alternance' => OffreRecrutement::TYPE_ALTERNANCE,
                ],
            ])
            ->add('competences', TextareaType::class, [
                'label' => 'Compétences recherchées',
                'required' => false,
                'attr' => ['rows' => 2],
            ])
            ->add('localisation', ChoiceType::class, [
                'label' => 'Localisation',
                'required' => false,
                'placeholder' => 'Sélectionnez un gouvernorat',
                'choices' => array_combine(self::GOUVERNORATS, self::GOUVERNORATS),
            ])
            ->add('teletravail', ChoiceType::class, [
                'label' => 'Télétravail possible',
                'choices' => ['Oui' => true, 'Non' => false],
            ])
            ->add('salaire', TextType::class, [
                'label' => 'Salaire (optionnel)',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: 1200-1500 TND/mois'],
            ])
            ->add('dateExpiration', DateType::class, [
                'label' => 'Date d\'expiration de l\'offre',
                'required' => false,
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => OffreRecrutement::class]);
    }
}
