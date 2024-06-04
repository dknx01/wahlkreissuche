<?php

namespace App\Controller\Api;

use App\ElectionPoster\Map;
use App\Service\Domain\ImageUploader;
use App\Service\Domain\WahlkreisHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/')]
class LocationController extends AbstractController
{
    #[Route('data/places/{state}/{city}', name: 'app_api_getplaces')]
    public function getPlaces(string $state, string $city, Map $map, ImageUploader $imageUploader): JsonResponse
    {
        $places = $this->getPosterPlaces($map, $state, $city, [], $imageUploader);

        return $this->json($places);
    }

    #[Route('data/places/{state}', name: 'app_api_places')]
    public function placesState(string $state, Map $map, ImageUploader $imageUploader): JsonResponse
    {
        $places = $map->findAllActivePostersInState($state);
        foreach ($places as $i => $details) {
            foreach ($details as $n => $detail) {
                $places[$i][$n] = [
                    'long' => $detail->getAddress()->getLongitude(),
                    'lat' => $detail->getAddress()->getLatitude(),
                    'desc' => $detail->getDescription(),
                    'district' => $detail->getAddress()->getDistrict(),
                    'edit' => $this->generateUrl('plakat_admin_edit', ['id' => $detail->getId()]),
                    'createdAt' => $detail->getCreatedAt()->format('d.m.Y H:i'),
                    'thumbnail' => $imageUploader->thumbnailAsBase64Data($detail->getThumbnailFilename()),
                ];
            }
        }

        return $this->json($places);
    }

    #[Route('data/places_wahllokale/{state}/{city}', name: 'app_api_getplaces_wahllokale')]
    public function getPlacesAndWahllokale(string $state, string $city, Map $map, ImageUploader $imageUploader): JsonResponse
    {
        $places = $this->getPosterPlaces($map, $state, $city, [], $imageUploader);
        $places['wahllokale'] = $map->findAllWahllokaleInCity('Berlin');

        return $this->json($places);
    }

    #[Route('data/agh_kreise', name: 'app_api_agh_kreise')]
    public function aghKreise(WahlkreisHandler $handler): Response
    {
        return new Response($handler->getAghWahlkreiseAsString());
    }

    #[Route('data/btw_kreise/{state}', name: 'app_api_btw_kreise')]
    public function btwKreise(string $state, WahlkreisHandler $handler): Response
    {
        return match ($state) {
            'Deutschland' => new Response($handler->getBtwWahlkreiseDeutschlandAsString()),
            'baden-württemberg' => 'Baden-Württemberg',
            'nordrhein-westfalen' => 'Nordrhein-Westfalen',
            'mecklenburg-vorpommern' => 'Mecklenburg-Vorpommern',
            'rheinland-pfalz' => 'Rheinland-Pfalz',
            'sachsen-anhalt' => 'Sachsen-Anhalt',
            'schleswig-holstein' => 'Schleswig-Holstein',
            default => new Response($handler->getBtwWahlkreiseAsStringByState(ucfirst($state)))
        };
    }

    #[Route('data/ltw_kreise/{state}', name: 'app_api_ltw_kreise')]
    public function ltwKreise(string $state, WahlkreisHandler $handler): Response
    {
        return new Response($handler->getLtwWahlkreiseAsStringByState($state));
    }

    /**
     * @param array<array-key, mixed> $places
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function getPosterPlaces(Map $map, string $state, string $city, array $places, ImageUploader $imageUploader): array
    {
        $placesData = $map->findAllActivePostersInDistricts($state, $city);

        foreach ($placesData as $district => $placeDetails) {
            $places['posters'][$district] = [];
            foreach ($placeDetails as $detail) {
                $places['posters'][$district][] = [
                    'long' => $detail->getAddress()->getLongitude(),
                    'lat' => $detail->getAddress()->getLatitude(),
                    'desc' => $detail->getDescription(),
                    'district' => $detail->getAddress()->getDistrict(),
                    'edit' => $this->generateUrl('plakat_admin_edit', ['id' => $detail->getId()]),
                    'createdAt' => $detail->getCreatedAt()->format('d.m.Y H:i'),
                    'thumbnail' => (string) $imageUploader->thumbnailAsBase64Data($detail->getThumbnailFilename()),
                ];
            }
        }

        return $places;
    }
}
