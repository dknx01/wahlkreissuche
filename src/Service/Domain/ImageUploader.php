<?php

namespace App\Service\Domain;

use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    public function __construct(private Filesystem $fs, private string $uploadFolder, private LoggerInterface $logger)
    {
    }

    public function uploadImagesAsThumbnail(UploadedFile $file, ?string $filename = null): string
    {
        $newFilename = $filename ?? (UuidV4::uuid4() . '.' . $file->guessExtension());

        try {
            $file->move(
                $this->uploadFolder,
                $newFilename
            );
            $fullFileName = $this->uploadFolder . $newFilename;
            $imagick = new \Imagick($fullFileName);
            $imagick->thumbnailImage(400, 200, true);
            $this->fs->dumpFile(
                $fullFileName,
                $imagick->getImageBlob()
            );
        } catch (FileException|\ImagickException $e) {
            $this->logger->error($e->getMessage());
        }

        return $newFilename;
    }

    public function thumbnailAsBase64Data(?string $fileName): ?string
    {
        if ($fileName === null) {
            return null;
        }
        $data = null;
        if ($this->fs->exists($this->uploadFolder . $fileName)) {
            $data = base64_encode(
                file_get_contents($this->uploadFolder . $fileName)
            );
        }

        return $data;
    }
}
