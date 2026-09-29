<?php

namespace App\Form;

use App\Entity\CandidatureFormateur;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;

class CandidatureFormateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matieres', EntityType::class, [
                'class' => Matiere::class,
                'label' => 'Matières que vous pouvez enseigner',
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
                'constraints' => [new Count(['min' => 1, 'minMessage' => 'Sélectionnez au moins une matière'])],
            ])
            ->add('stacks', TextareaType::class, [
                'label' => 'Technologies maîtrisées',
                'required' => false,
                'attr' => ['rows' => 2],
            ])
            ->add('experience', TextareaType::class, [
                'label' => 'Expérience professionnelle / pédagogique',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('diplomes', TextareaType::class, [
                'label' => 'Diplômes',
                'required' => false,
                'attr' => ['rows' => 2],
            ])
            ->add('cvFile', FileType::class, [
                'label' => 'CV (PDF ou Word, 5 Mo max)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ],
                        'mimeTypesMessage' => 'Merci de déposer un fichier PDF ou Word',
                    ]),
                ],
            ])
            ->add('disponibiliteHeures', IntegerType::class, [
                'label' => 'Disponibilité (heures / semaine)',
                'required' => false,
                'attr' => ['min' => 1],
            ])
            ->add('tarifHoraire', NumberType::class, [
                'label' => 'Tarif horaire souhaité (TND, laisser vide si négociable)',
                'required' => false,
                'html5' => true,
                'attr' => ['min' => 0, 'step' => 0.5],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CandidatureFormateur::class]);
    }
}
