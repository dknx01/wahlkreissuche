<?php

namespace App\Service\Domain;

use App\Repository\ElectionPosterRepository;
use App\Repository\WishElectionPosterRepository;

class ManualLocationHandler
{
    /** @var array<string, string> */
    private array $submittedCity = [];
    /** @var array<string, string> */
    private array $submittedDistrict = [];

    public function __construct(private ElectionPosterRepository $repository, private WishElectionPosterRepository $wishElectionPosterRepository)
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
    public function getCityChoicesForWishes(): array
    {
        return array_merge(
            array_combine(array_values($this->wishElectionPosterRepository->findAllCities()), $this->wishElectionPosterRepository->findAllCities()),
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

    /**
     * @return array<string, string>
     */
    public function getDistrictChoicesWishes(): array
    {
        $districts = $this->wishElectionPosterRepository->findAllDistricts();

        return array_merge(
            array_combine(array_values($districts), $districts),
            $this->submittedDistrict
        );
    }
}
