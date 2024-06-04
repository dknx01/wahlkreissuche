<?php

declare(strict_types=1);

namespace App\UnitTests\Security\Voter;

use App\Entity\User;
use App\Security\Permissions;
use App\Security\Roles;
use App\Security\Voter\UserVoter;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserVoterTest extends TestCase
{
    use ProphecyTrait;
    private ObjectProphecy|Security $security;

    protected function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);
    }

    public function testVoterSuccessful(): void
    {
        $voter = new UserVoter($this->security->reveal());

        $user = new User();
        $user->setActive(true);
        $user->setIsVerified(true);
        $token = new UsernamePasswordToken($user, 'foo');
        $this->security->isGranted(Roles::USER)->willReturn(true);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, [Permissions::USER_FULLY_ACTIVE]));
    }

    #[TestWith([false, true, true, Permissions::USER_FULLY_ACTIVE])]
    #[TestWith([true, true, true, Permissions::ACTIVE])]
    #[TestWith([true, true, false, Permissions::USER_FULLY_ACTIVE])]
    #[TestWith([true, false, true, Permissions::USER_FULLY_ACTIVE])]
    #[TestWith([false, false, true, Permissions::USER_FULLY_ACTIVE])]
    #[TestWith([false, false, false, Permissions::USER_FULLY_ACTIVE])]
    public function testVoterNonSuccessfulWithUser(bool $active, bool $verified, bool $isGranted, string $permission): void
    {
        $voter = new UserVoter($this->security->reveal());

        $user = new User();
        $user->setActive($active);
        $user->setIsVerified($verified);
        $token = new UsernamePasswordToken($user, 'foo');
        $this->security->isGranted(Roles::USER)->willReturn($isGranted);

        $this->assertNotEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, [$permission]));
    }

    public function testVoterNonSuccessfulWithoutUser(): void
    {
        $voter = new UserVoter($this->security->reveal());

        $token = new NullToken();
        $this->security->isGranted(Roles::USER)->shouldNotBeCalled();

        $this->assertNotEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, [Permissions::USER_FULLY_ACTIVE]));
    }
}
