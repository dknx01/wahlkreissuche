<?php

namespace App\Service\Domain;

use App\Repository\WishElectionPosterRepository;

class WishManualLocationHandler
{
    /** @var array<string, string> */
    private array $submittedCity = [];
    /** @var array<string, string> */
    private array $submittedDistrict = [];

    public function __construct(private WishElectionPosterRepository $repository)
    {
    }

    public function setCity(string $data): void
    {
        $this->submittedCity = [$data => $data];
    }

    public function setDistrict(string $data): void
    {
        $this->submittedDistrict = [$data => $data];
    }

    /**
     * @return array<string, string>
     */
    public function getCityChoices(): array
    {
        return array_merge(
            array_combine(array_values($this->repository->findAllCities()), $this->repository->findAllCities()),
            $this->submittedCity
        );
    }

    /**
     * @return array<string, string>
     */
    public function getDistrictChoices(): array
    {
        $districts = $this->repository->findAllDistricts();

        return array_merge(
            array_combine(array_values($districts), $districts),
            $this->submittedDistrict
        );
    }
}
