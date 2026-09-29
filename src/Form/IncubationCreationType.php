<?php

namespace App\Form;

use App\Entity\Incubation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncubationCreationType extends AbstractType
{
    /**
     * Liste fermée non fournie par les tickets (le Ticket 1.4 renvoie au Ticket 1.5,
     * qui ne la définit pas non plus) — nomenclature à valider avec l'équipe métier,
     * sur le même principe que les gouvernorats du Ticket 2.2.
     */
    public const SECTEURS = [
        'Agro-alimentaire', 'Technologies / IT', 'Artisanat', 'Textile',
        'Tourisme', 'Santé', 'Éducation', 'Commerce', 'Industrie',
        'Services', 'Agriculture', 'BTP / Construction', 'Environnement', 'Autre',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('secteurActivite', ChoiceType::class, [
                'label' => 'Secteur d\'activité',
                'placeholder' => 'Sélectionnez un secteur',
                'choices' => array_combine(self::SECTEURS, self::SECTEURS),
            ])
            ->add('stadeMaturite', ChoiceType::class, [
                'label' => 'Stade de maturité',
                'choices' => [
                    'Idée' => Incubation::STADE_IDEE,
                    'MVP' => Incubation::STADE_MVP,
                    'Lancé' => Incubation::STADE_LANCE,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Incubation::class]);
    }
}
