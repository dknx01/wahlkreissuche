<?php

namespace App\Service\Domain;

use App\Entity\WishElectionPoster;
use App\Repository\WishElectionPosterRepository;

class WishElectionPosterLocationHandler
{
    public function __construct(private WishElectionPosterRepository $repository)
    {
    }

    public function saveEntity(WishElectionPoster $wishElectionPoster): void
    {
        $this->repository->save($wishElectionPoster);
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllPlakatOrte(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @return WishElectionPoster[]
     */
    public function findAllWishPlacesByUser(string $user, string $activeOnly): array
    {
        return $this->repository->findAllByUser($user, $activeOnly);
    }
}
