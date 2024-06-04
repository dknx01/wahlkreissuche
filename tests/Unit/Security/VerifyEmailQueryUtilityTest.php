<?php

declare(strict_types=1);

namespace App\UnitTests\Security;

use App\Security\VerifyEmailQueryUtility;
use PHPUnit\Framework\TestCase;

class VerifyEmailQueryUtilityTest extends TestCase
{
    public function testGetTokenFromQuery(): void
    {
        $verifyEmailQueryUtility = new VerifyEmailQueryUtility();

        $uri = 'http://www.iona-durgan.co?token=0A0AF005&expires=1234567890';
        $rs = $verifyEmailQueryUtility->getTokenFromQuery($uri);
        $this->assertEquals('0A0AF005', $rs);
    }

    public function testGetExpiryTimestamp(): void
    {
        $verifyEmailQueryUtility = new VerifyEmailQueryUtility();

        $uri = 'http://www.iona-durgan.co?token=0A0AF005&expires=1234567890';
        $rs = $verifyEmailQueryUtility->getExpiryTimestamp($uri);
        $this->assertEquals('1234567890', $rs);
    }

    public function testGetExpiryTimestampWithoutTimestamp(): void
    {
        $verifyEmailQueryUtility = new VerifyEmailQueryUtility();

        $uri = 'http://www.iona-durgan.co?token=0A0AF005&expires=';
        $rs = $verifyEmailQueryUtility->getExpiryTimestamp($uri);
        $this->assertEquals('0', $rs);
    }

    public function testGetExpiryTimestampWithStrangeFormat(): void
    {
        $verifyEmailQueryUtility = new VerifyEmailQueryUtility();

        $uri = 'http://www.iona-durgan.co?token=0A0AF005&?expires=12';
        $rs = $verifyEmailQueryUtility->getExpiryTimestamp($uri);
        $this->assertEquals('12', $rs);
    }
}
