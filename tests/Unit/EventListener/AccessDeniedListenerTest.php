<?php

declare(strict_types=1);

namespace App\UnitTests\EventListener;

use App\EventListener\AccessDeniedListener;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class AccessDeniedListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testOnKernelExceptionWithNonAccessDeniedException(): void
    {
        $twig = $this->prophesize(Environment::class);
        $listener = new AccessDeniedListener($twig->reveal());
        $event = new ExceptionEvent(
            $this->prophesize(HttpKernelInterface::class)->reveal(),
            $this->prophesize(Request::class)->reveal(),
            HttpKernelInterface::MAIN_REQUEST,
            new \Exception()
        );
        $listener->onKernelException($event);
        $this->assertNull($event->getResponse());
    }

    public function testOnKernelExceptionWithAccessDeniedException(): void
    {
        $twig = $this->prophesize(Environment::class);
        $html = '<html lang="de">ERROR</html>';
        $twig->render('security/security_exception.html.twig')->willReturn($html);
        $listener = new AccessDeniedListener($twig->reveal());
        $event = new ExceptionEvent(
            $this->prophesize(HttpKernelInterface::class)->reveal(),
            $this->prophesize(Request::class)->reveal(),
            HttpKernelInterface::MAIN_REQUEST,
            new AccessDeniedException()
        );
        $listener->onKernelException($event);
        $this->assertSame($html, $event->getResponse()->getContent());
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertEquals(
            [KernelEvents::EXCEPTION => ['onKernelException', 2]],
            AccessDeniedListener::getSubscribedEvents()
        );
    }
}
