<?php

namespace App\ElectionPoster;

use App\Entity\Wahllokal;
use App\Entity\WishElectionPoster;
use App\Repository\WahllokalRepository;
use App\Repository\WishElectionPosterRepository;

readonly class WishMap
{
    public function __construct(
        private WishElectionPosterRepository $wishElectionPosterRepository,
        private WahllokalRepository $wahllokalRepository
    ) {
    }

    /**
     * @return array<string, array<array-key, WishElectionPoster>>
     */
    public function findAllActivePostersInDistricts(string $state, string $city): array
    {
        $districtData = [];
        /** @var array<'district', string> $district */
        foreach ($this->wishElectionPosterRepository->findAllDistrictsByCityAndState($state, $city) as $district) {
            $districtData[$district['district']] = $this->wishElectionPosterRepository
                ->findAllActiveByDistrictAndCityAndState($district['district'], $city, $state);
        }

        return $districtData;
    }

    /**
     * @return array<string, array<array-key, WishElectionPoster>>
     */
    public function findAllActivePostersInState(string $state): array
    {
        $stateData = [];
        /** @var array<'city', string> $city */
        foreach ($this->wishElectionPosterRepository->findAllCitiesByState($state) as $city) {
            $stateData[$city['city']] = $this->wishElectionPosterRepository
                ->findAllActiveByStateAndCity($city['city'], $state);
        }

        return $stateData;
    }

    /**
     * @return array<array-key, Wahllokal>
     */
    public function findAllWahllokale(): array
    {
        return $this->wahllokalRepository->findAll();
    }
}
