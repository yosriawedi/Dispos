<?php

namespace App\Security\Voter;

use App\Entity\Incubation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Une entreprise ne peut consulter que son propre dossier d'incubation.
 * Les admins conservent un accès total (gestion via /admin).
 */
class IncubationVoter extends Voter
{
    public const VIEW = 'INCUBATION_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Incubation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Incubation $subject */
        return $subject->getEntreprise() === $user;
    }
}
