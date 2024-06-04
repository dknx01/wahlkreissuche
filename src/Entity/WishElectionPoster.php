<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ElectionPoster\Address;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;

use function Symfony\Component\String\u;

#[ORM\Entity]
class WishElectionPoster
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = '';
    #[ORM\Column(type: 'string', nullable: true)]
    private string $createdBy;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeInterface $createdAt;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeInterface $deletedAt;
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active;
    #[ORM\Embedded(class: Address::class)]
    private Address $address;
    #[ORM\Column(type: 'string', nullable: true)]
    private string|File|null $thumbnailFilename;

    public function __construct(
        string $createdBy,
        \DateTimeInterface $createdAt,
        Address $address,
        ?string $description = null,
        ?\DateTimeInterface $deletedAt = null,
        bool $active = true,
    ) {
        $this->setDescription($description);
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
        $this->deletedAt = $deletedAt;
        $this->active = $active;
        $this->address = $address;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description ?? '';
        $this->description = u($this->description)
            ->collapseWhitespace()
            ->trim()
            ->replace('/.*\x0D0A.*/i', '')
            ->replace('/.*\x0D4D.*/i', '')
            ->toString()
        ;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getThumbnailFilename(): string|File|null
    {
        return $this->thumbnailFilename;
    }

    public function setThumbnailFilename(string|File|null $thumbnailFilename): void
    {
        $this->thumbnailFilename = $thumbnailFilename;
    }
}
