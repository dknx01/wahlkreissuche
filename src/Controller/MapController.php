<?php

namespace App\Controller;

use App\Options\BtwKreise;
use App\ElectionPoster\Map;
use App\Service\Domain\WahlkreisHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MapController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/plakate/{state}/{city}', name: 'plakate')]
    public function index(string $state, string $city, Map $map): Response
    {
        return $this->render('plakatorte/showAll.html.twig', [
            'places' => $map->findAllActivePostersInDistricts($state, $city),
            'city' => $city,
            'state' => $state,
            'values' => [
                'url' => $this->generateUrl('app_api_getplaces', ['state' => $state, 'city' => $city]),
            ],
        ]);
    }

    #[Route('/plakate/mitWunschorten/{state}/{city}', name: 'plakate_with_wishes')]
    public function indexWithWishes(string $state, string $city, Map $map): Response
    {
        return $this->render('plakatorte/showAllWithWishes.html.twig', [
            'places' => $map->findAllActivePostersInDistrictsWithWishes($state, $city),
            'city' => $city,
            'state' => $state,
        ]);
    }

    #[Route('/plakate/{state}', name: 'plakate_state')]
    public function state(string $state): Response
    {
        return $this->render('plakatorte/showAllPlakateWithoutDistrict.html.twig', [
            'state' => $state,
            'values' => [
                'url' => $this->generateUrl('app_api_places', ['state' => $state]),
                'mapCenter' => BtwKreise::getConfig($state),
            ],
        ]);
    }

    #[Route('/wahllokale/berlin', name: 'wahllokale_berlin')]
    public function wahllokale(Map $map): Response
    {
        return $this->render('wahllokale/showAll_wahllokale.html.twig', [
            'places' => $map->findAllActivePostersInDistricts('Berlin', 'Berlin'),
            'values' => [
                'url' => $this->generateUrl('app_api_getplaces_wahllokale', ['state' => 'Berlin', 'city' => 'Berlin']),
                'city' => 'Berlin',
                'state' => 'Berlin',
            ],
        ]);
    }

    #[Route('/agh_kreise', name: 'agh_kreise')]
    public function wahlkreiseAgh(WahlkreisHandler $handler): Response
    {
        return $this->render(
            'wahlkreise/agh.html.twig',
            [
                'values' => [
                    'url' => $this->generateUrl('app_api_agh_kreise'),
                ],
            ]
        );
    }

    #[Route('/btw_kreis/berlin', name: 'btw_kreise_berlin')]
    public function wahlkreiseBtwBerlin(WahlkreisHandler $handler): Response
    {
        return $this->render(
            'wahlkreise/btw.html.twig',
            [
                'state' => 'Berlin',
                'values' => [
                    'url' => $this->generateUrl('app_api_btw_kreise', ['state' => 'Berlin']),
                    'mapConfig' => [
                        'zoom' => 11,
                        'lon' => 13.3922187,
                        'lat' => 52.516215,
                    ],
                ],
                'title' => $this->translator->trans('areas_btw_state', ['{{state}}' => 'Berlin'], 'maps'),
            ]
        );
    }

    #[Route('/btw_kreise/deutschland', name: 'btw_kreise_deutschland')]
    public function wahlkreiseBtwGermany(WahlkreisHandler $handler, TranslatorInterface $translator): Response
    {
        return $this->render(
            'wahlkreise/btw.html.twig',
            [
                'state' => 'Deutschland',
                'values' => [
                    'url' => $this->generateUrl('app_api_btw_kreise', ['state' => 'Deutschland']),
                    'mapconfig' => BtwKreise::getConfig(''),
                ],
                'title' => $translator->trans('areas_btw_germany', [], 'maps'),
            ]
        );
    }

    #[Route('/btw_kreise/{state}', name: 'btw_kreise')]
    public function wahlkreiseBtwByState(string $state, TranslatorInterface $translator): Response
    {
        $state = match ($state) {
            'baden-württemberg' => 'Baden-Württemberg',
            'nordrhein-westfalen' => 'Nordrhein-Westfalen',
            'mecklenburg-vorpommern' => 'Mecklenburg-Vorpommern',
            'rheinland-pfalz' => 'Rheinland-Pfalz',
            'sachsen-anhalt' => 'Sachsen-Anhalt',
            'schleswig-holstein' => 'Schleswig-Holstein',
            default => ucfirst($state)
        };

        return $this->render(
            'wahlkreise/btw.html.twig',
            [
                'state' => $state,
                'values' => [
                    'url' => $this->generateUrl('app_api_btw_kreise', ['state' => $state]),
                    'mapconfig' => BtwKreise::getConfig($state),
                ],
                'title' => $translator->trans('areas_btw_state', ['{{state}}' => $state], 'maps'),
            ]
        );
    }

    #[Route('/ltw_kreise/{state}', name: 'ltw_kreise')]
    public function wahlkreiseLtwByState(string $state, WahlkreisHandler $handler, TranslatorInterface $translator): Response
    {
        return $this->render(
            'wahlkreise/ltw.html.twig',
            [
                'state' => $state,
                'values' => [
                    'mapconfig' => BtwKreise::getConfig($state),
                    'url' => $this->generateUrl('app_api_ltw_kreise', ['state' => $state]),
                ],
                'title' => $translator->trans('areas_ltw_state', ['{{state}}' => $state], 'maps'),
            ]
        );
    }
}
