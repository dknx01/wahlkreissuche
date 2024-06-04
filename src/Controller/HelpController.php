<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hilfe')]
class HelpController extends AbstractController
{
    #[Route('/', name: 'help_index')]
    public function index(): Response
    {
        return $this->render('help/overview.html.twig');
    }

    #[Route('/suche', name: 'help_search')]
    public function search(): Response
    {
        return $this->render('help/search.html.twig');
    }

    #[Route('/benutzer', name: 'help_user')]
    public function user(): Response
    {
        return $this->render('help/user.html.twig');
    }

    #[Route('/karten', name: 'help_maps')]
    public function maps(): Response
    {
        return $this->render('help/maps.html.twig');
    }
}
