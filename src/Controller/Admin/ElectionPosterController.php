<?php

namespace App\Controller\Admin;

use App\Repository\ElectionPosterRepository;
use App\Security\Permissions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/election_posters')]
class ElectionPosterController extends AbstractController
{
    #[Route('/bundesland/{bundesland}/{activeOnly}', name: 'admin_election_posters_state', defaults: ['activeOnly' => ''])]
    public function state(string $bundesland, string $activeOnly, ElectionPosterRepository $repo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render(
            'plakatorte/overview_state.html.twig',
            [
                'state' => $bundesland,
                'posters' => $repo->findAllByState($bundesland, $activeOnly),
                'activeOnly' => $activeOnly === 'active',
            ]
        );
    }

    #[Route('/ort/{city}/{activeOnly}', name: 'election_posters_city', defaults: ['activeOnly' => ''])]
    public function city(string $activeOnly, string $city, ElectionPosterRepository $repo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render(
            'plakatorte/overview_city.html.twig',
            [
                'city' => $city,
                'posters' => $repo->findAllByCity($city, $activeOnly),
                'activeOnly' => $activeOnly === 'active',
            ]
        );
    }

    #[Route('/ort_teil/{city}/{district}/{activeOnly}', name: 'election_posters_state_district', defaults: ['activeOnly' => ''])]
    public function cityDistrict(string $activeOnly, string $city, string $district, ElectionPosterRepository $repo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render(
            'plakatorte/overview_city_district.html.twig',
            [
                'city' => $city,
                'district' => $district,
                'posters' => $repo->findAllByCityAndDistrict($city, $district, $activeOnly),
                'activeOnly' => $activeOnly === 'active',
            ]
        );
    }

    #[Route('/alle/{activeOnly}', name: 'election_posters_all', defaults: ['activeOnly' => ''])]
    public function all(string $activeOnly, ElectionPosterRepository $repo): Response
    {
        $this->denyAccessUnlessGranted(Permissions::USER_FULLY_ACTIVE);

        return $this->render(
            'plakatorte/overview_all.htm.twig',
            [
                'posters' => $repo->findAllForOverview($activeOnly),
                'activeOnly' => $activeOnly === 'active',
            ]
        );
    }
}
