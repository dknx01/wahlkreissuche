<?php

namespace App\Controller\Userbackend;

use App\ElectionPoster\WishMap;
use App\Security\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('wunsch-orte', name: 'wishes')]
class WishMapController extends AbstractController
{
    #[Route('/plakate/{state}', name: 'plakate_state')]
    public function state(string $state, WishMap $map): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render('plakatorte/wishes/showAllPlakateWithoutDistrict.html.twig', [
            'places' => $map->findAllActivePostersInState($state),
            'state' => $state,
        ]);
    }

    #[Route('/plakate/{state}/{city}', name: 'plakate_state_city')]
    public function stateCity(string $state, string $city, WishMap $map): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render('plakatorte/wishes/showAllPlakateWithoutDistrict.html.twig', [
            'places' => $map->findAllActivePostersInDistricts($state, $city),
            'state' => $state,
        ]);
    }
}
