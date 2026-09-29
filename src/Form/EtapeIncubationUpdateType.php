<?php

namespace App\Form;

use App\Entity\EtapeIncubation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Réservé admin/référent — mise à jour du statut et du commentaire d'une étape.
 * L'étudiant/entreprise n'a jamais accès à ce formulaire (cf. IncubationController).
 */
class EtapeIncubationUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Non démarrée' => EtapeIncubation::STATUT_NON_DEMARREE,
                    'En cours' => EtapeIncubation::STATUT_EN_COURS,
                    'En attente de validation' => EtapeIncubation::STATUT_EN_ATTENTE_VALIDATION,
                    'Validée' => EtapeIncubation::STATUT_VALIDEE,
                    'Bloquée' => EtapeIncubation::STATUT_BLOQUEE,
                ],
            ])
            ->add('commentaireAdmin', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
                'attr' => ['rows' => 3],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => EtapeIncubation::class]);
    }
}
