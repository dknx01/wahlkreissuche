<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class VerifiedVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === Permissions::VERIFIED;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $user->isVerified();
    }
}
