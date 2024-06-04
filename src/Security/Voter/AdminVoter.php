<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\Permissions;
use App\Security\Roles;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminVoter extends Voter
{
    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === Permissions::USER_ADMIN;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $this->security->isGranted(Roles::ADMIN)
            && $user->getActive()
            && $user->isVerified();
    }
}
