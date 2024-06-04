<?php

declare(strict_types=1);

namespace App\UnitTests\Security\Voter;

use App\Entity\User;
use App\Security\Permissions;
use App\Security\Voter\VerifiedVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class VerifiedVoterTest extends TestCase
{
    #[DataProvider('provideAttribute')]
    public function testVoter(string $permission, TokenInterface $token, int $expectedValue): void
    {
        $voter = new VerifiedVoter();
        $this->assertEquals($expectedValue, $voter->vote($token, null, [$permission]));
    }

    public static function provideAttribute(): array|\Generator
    {
        $activeUser = new User();
        $activeUser->setIsVerified(true);
        $inactiveUser = new User();
        $inactiveUser->setIsVerified(false);

        return [
            yield [Permissions::VERIFIED, new UsernamePasswordToken($activeUser, 'foo'), VoterInterface::ACCESS_GRANTED],
            yield [Permissions::ACTIVE, new NullToken(), VoterInterface::ACCESS_ABSTAIN],
            yield [Permissions::ACTIVE, new UsernamePasswordToken($inactiveUser, 'foo'), VoterInterface::ACCESS_ABSTAIN],
            yield [Permissions::VERIFIED, new NullToken(), VoterInterface::ACCESS_DENIED],
            yield ['Permissions::VERIFIED', new UsernamePasswordToken($activeUser, 'foo'), VoterInterface::ACCESS_ABSTAIN],
        ];
    }
}
