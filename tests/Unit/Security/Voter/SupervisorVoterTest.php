<?php

declare(strict_types=1);

namespace App\UnitTests\Security\Voter;

use App\Entity\User;
use App\Security\Permissions;
use App\Security\Roles;
use App\Security\Voter\SupervisorVoter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SupervisorVoterTest extends TestCase
{
    use ProphecyTrait;
    private ObjectProphecy|Security $security;

    protected function setUp(): void
    {
        $this->security = $this->prophesize(Security::class);
    }

    public function testVoterSuccessful(): void
    {
        $voter = new SupervisorVoter($this->security->reveal());

        $user = new User();
        $user->setActive(true);
        $user->setIsVerified(true);
        $token = new UsernamePasswordToken($user, 'foo');
        $this->security->isGranted(Roles::SUPERVISOR)->willReturn(true);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, null, [Permissions::USER_SUPERVISOR]));
    }

    public function testVoterForNonUserObject(): void
    {
        $voter = new SupervisorVoter($this->security->reveal());

        $token = new NullToken();
        $this->security->isGranted(Roles::SUPERVISOR)->willReturn(true);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, null, [Permissions::USER_SUPERVISOR]));
    }
}
