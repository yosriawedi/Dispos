<?php

namespace App\Security\Voter;

use App\Entity\EtapeIncubation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Seul le référent affecté à une étape (n'importe quel rôle) ou un admin
 * peut la mettre à jour.
 */
class EtapeIncubationVoter extends Voter
{
    public const EDIT = 'ETAPE_INCUBATION_EDIT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT && $subject instanceof EtapeIncubation;
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

        /** @var EtapeIncubation $subject */
        return $subject->getReferent() === $user;
    }
}
