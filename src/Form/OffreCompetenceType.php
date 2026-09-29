<?php

namespace App\Form;

use App\Entity\OffreCompetence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OffreCompetenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la compétence proposée',
                'constraints' => [new NotBlank(['message' => 'Le titre est requis'])],
                'attr' => ['placeholder' => 'Ex: Développement d\'un site vitrine WordPress'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du service / livrable proposé',
                'constraints' => [new NotBlank(['message' => 'La description est requise'])],
                'attr' => ['rows' => 4],
            ])
            ->add('stack', TextareaType::class, [
                'label' => 'Stack / outils maîtrisés',
                'required' => false,
                'attr' => ['rows' => 2, 'placeholder' => 'Ex: WordPress, Figma, Photoshop...'],
            ])
            ->add('heuresEstimees', IntegerType::class, [
                'label' => 'Estimation du temps de travail (heures)',
                'required' => false,
                'attr' => ['min' => 1],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => OffreCompetence::class]);
    }
}
