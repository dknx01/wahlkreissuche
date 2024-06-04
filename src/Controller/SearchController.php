<?php

namespace App\Controller;

use App\Options\BtwKreise;
use App\Repository\WahlkreisRepository;
use App\Service\Domain\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/', name: 'search')]
    public function search(): Response
    {
        return $this->render(
            'search/search.html.twig',
            [
                'states' => BtwKreise::getStates(),
            ]
        );
    }

    #[Route(path: '/suche/ergebnis', name: 'suche_result')]
    public function searchResult(Request $request, Search $search, WahlkreisRepository $repository): Response
    {
        $data = $search->searchByAddress($request->request->get('address'), $request->request->get('state'));
        /* @var Search\Geofence $entry */

        return $this->render('wahlkreise/search_result.html.twig', [
            'election_areas' => $data,
        ]);
    }

    #[Route(path: '/suche/map/{data}', name: 'suche_map')]
    public function map(string $data): Response
    {
        return $this->render('wahlkreise/map.html.twig', [
            'searchResults' => base64_decode($data),
        ]);
    }
}
