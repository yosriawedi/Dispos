<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [new NotBlank(['message' => 'Le prénom est requis'])],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [new NotBlank(['message' => 'Le nom est requis'])],
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
                'required' => false,
            ])
            ->add('avatar', TextType::class, [
                'label' => 'Photo de profil (URL)',
                'required' => false,
                'attr' => ['placeholder' => 'https://...'],
            ])
            ->add('locale', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Français' => 'fr',
                    'English' => 'en',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
