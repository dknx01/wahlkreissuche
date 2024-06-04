<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Domain\ImageUploader;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MapExtension extends AbstractExtension
{
    public function __construct(private ImageUploader $imageUploader)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('thumbnail', [$this, 'thumbnailAsBase64Data']),
        ];
    }

    public function thumbnailAsBase64Data(?string $filename = null): ?string
    {
        return $this->imageUploader->thumbnailAsBase64Data($filename);
    }
}
