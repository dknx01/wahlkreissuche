<?php

namespace App\ElectionPoster;

use App\Entity\ElectionPoster;
use App\Entity\Wahllokal;
use App\Entity\WishElectionPoster;
use App\Repository\ElectionPosterRepository;
use App\Repository\WahllokalRepository;
use App\Repository\WishElectionPosterRepository;

readonly class Map
{
    public function __construct(
        private ElectionPosterRepository $electionPosterRepo,
        private WahllokalRepository $wahllokalRepo,
        private WishElectionPosterRepository $wishElectionPosterRepo
    ) {
    }

    /**
     * @return array<string, array<array-key, ElectionPoster>>
     */
    public function findAllActivePostersInDistricts(string $state, string $city): array
    {
        $districtData = [];
        /** @var array<'district', string> $district */
        foreach ($this->electionPosterRepo->findAllDistrictsByCityAndState($state, $city) as $district) {
            $districtData[$district['district']] = $this->electionPosterRepo
                ->findAllActiveByDistrictAndCityAndState($district['district'], $city, $state);
        }

        return $districtData;
    }

    /**
     * @return array<string, array<array-key, ElectionPoster>>
     */
    public function findAllActivePostersInState(string $state): array
    {
        $stateData = [];
        /** @var array<'city', string> $city */
        foreach ($this->electionPosterRepo->findAllCitiesByState($state) as $city) {
            $stateData[$city['city']] = $this->electionPosterRepo
                ->findAllActiveByStateAndCity($city['city'], $state);
        }

        return $stateData;
    }

    /**
     * @return array<array-key, Wahllokal>
     */
    public function findAllWahllokale(): array
    {
        return $this->wahllokalRepo->findAll();
    }

    /**
     * @return array<array-key, array<array-key, ElectionPoster|WishElectionPoster>>
     */
    public function findAllActivePostersInDistrictsWithWishes(string $state, string $city): array
    {
        $districtData = [];
        /** @var array<'district', string> $district */
        foreach ($this->electionPosterRepo->findAllDistrictsByCityAndState($state, $city) as $district) {
            $districtData[$district['district']] = $this->electionPosterRepo
                ->findAllActiveByDistrictAndCityAndState($district['district'], $city, $state);
        }
        $districtData['Wunschorte'] = $this->wishElectionPosterRepo->findAllActiveByStateAndCity($city, $state);

        return $districtData;
    }

    /**
     * @return array<array-key, Wahllokal>
     */
    public function findAllWahllokaleInCity(string $city): array
    {
        return $this->wahllokalRepo->findBy(['city' => $city]);
    }
}
