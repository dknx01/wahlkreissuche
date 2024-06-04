<?php

namespace App\Service\Domain;

use App\Entity\ElectionPoster;
use App\Repository\ElectionPosterRepository;

class ElectionPosterHandler
{
    public function __construct(private ElectionPosterRepository $electionPosterRepository)
    {
    }

    public function saveEntity(ElectionPoster $plakatOrte): void
    {
        $this->electionPosterRepository->save($plakatOrte);
    }

    /**
     * @return ElectionPoster[]
     */
    public function findAllPlakatOrte(): array
    {
        return $this->electionPosterRepository->findAll();
    }
}
