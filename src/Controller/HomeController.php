<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/impressum', name: 'impressum')]
    public function impressum(): Response
    {
        return $this->render('defaults/impressum.html.twig');
    }

    #[Route('/cookie-benutzung', name: 'cookies')]
    public function cookies(): Response
    {
        return $this->render('defaults/cookies.html.twig');
    }

    #[Route('/adress-quellen', name: 'quellen')]
    public function quellen(): Response
    {
        return $this->render('defaults/quellen.html.twig');
    }

    #[Route('/nutzungsbedingungen', name: 'terms')]
    public function terms(): Response
    {
        return $this->render('defaults/terms.html.twig');
    }
}
