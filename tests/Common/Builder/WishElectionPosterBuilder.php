<?php

namespace App\Tests\Builder;

use App\Entity\ElectionPoster\Address;
use App\Entity\WishElectionPoster;

/**
 * @implements WishElectionPoster
 */
class WishElectionPosterBuilder implements BuilderInterface
{
    use FakerTrait;

    public function __construct(
        private ?string $createdBy = null,
        private ?\DateTimeInterface $createdAt = null,
        private ?string $description = null,
        private ?string $thumbnailFilename = null,
        private bool $active = true,
        private ?\DateTimeInterface $deletedAt = null,
        private ?Address $address = null,
    ) {
    }

    public function get(): WishElectionPoster
    {
        return new WishElectionPoster(
            createdBy: $this->createdBy ?? self::getFaker()->name(),
            createdAt: $this->createdAt ?? self::getFaker()->dateTime()->modify('-1 day'),
            address: $this->address ?? (new AddressBuilder())->get(),
            description: $this->description,
            deletedAt: $this->deletedAt,
            active: $this->active,
        );
    }
}
