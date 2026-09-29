<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Changement de mot de passe depuis le profil (utilisateur déjà connecté,
 * donc on exige le mot de passe actuel plutôt qu'un token email).
 */
class ChangeOwnPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'constraints' => [new NotBlank(['message' => 'Merci de saisir votre mot de passe actuel'])],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => ['placeholder' => 'Minimum 8 caractères'],
                    'constraints' => [
                        new NotBlank(['message' => 'Merci de saisir un nouveau mot de passe']),
                        new Length(['min' => 8, 'minMessage' => 'Minimum {{ limit }} caractères']),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le nouveau mot de passe',
                ],
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
