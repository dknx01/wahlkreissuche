<?php

declare(strict_types=1);

namespace App\UnitTests\Service\Domain;

use App\Service\Domain\ImageUploader;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderTest extends TestCase
{
    use ProphecyTrait;

    public function testUploadImagesAsThumbnailWithoutFilename(): void
    {
        $fileName = 'quidem.jpeg';

        $root = vfsStream::setup('test');
        $file = vfsStream::newFile($fileName)
            ->withContent(file_get_contents(__DIR__ . '/150.png'))
            ->at($root);

        $uploader = new ImageUploader(new Filesystem(), vfsStream::url('test') . '/', $this->prophesize(LoggerInterface::class)->reveal());

        $uploadedFile = new UploadedFile(
            path: vfsStream::url('test') . '/' . $file->getName(),
            originalName: $fileName,
            test: true
        );
        $result = $uploader->uploadImagesAsThumbnail($uploadedFile);
        $this->assertNotEmpty($result);
        $this->assertTrue($root->hasChild($result));
        $this->assertGreaterThan(0, $root->getChild($result)->size());
    }

    public function testUploadImagesAsThumbnailWithFilename(): void
    {
        $fileName = 'quidem.jpeg';
        $fileNameSaved = 'possimus.png';

        $root = vfsStream::setup('test');
        $file = vfsStream::newFile($fileName)
            ->withContent(file_get_contents(__DIR__ . '/150.png'))
            ->at($root);

        $uploader = new ImageUploader(new Filesystem(), vfsStream::url('test') . '/', $this->prophesize(LoggerInterface::class)->reveal());

        $uploadedFile = new UploadedFile(
            path: vfsStream::url('test') . '/' . $file->getName(),
            originalName: $fileName,
            test: true
        );
        $result = $uploader->uploadImagesAsThumbnail($uploadedFile, $fileNameSaved);
        $this->assertEquals($fileNameSaved, $result);
        $this->assertTrue($root->hasChild($result));
        $this->assertGreaterThan(0, $root->getChild($result)->size());
    }

    public function testUploadImagesAsThumbnailWithException(): void
    {
        $fileName = 'quidem.jpeg';

        $root = vfsStream::setup('test');
        vfsStream::newDirectory('store')
            ->chmod(000)
            ->at($root);
        $file = vfsStream::newFile($fileName)
            ->withContent('A6762203')
            ->at($root);

        $logger = $this->prophesize(LoggerInterface::class);
        $logger->error(Argument::type('string'));
        $uploader = new ImageUploader(new Filesystem(), vfsStream::url('test/store'), $logger->reveal());

        $uploadedFile = new UploadedFile(
            path: vfsStream::url('test') . '/' . $file->getName(),
            originalName: $fileName,
            test: true
        );

        $result = $uploader->uploadImagesAsThumbnail($uploadedFile);
        $this->assertNotEmpty($result);
        $this->assertFalse($root->hasChild($result));
    }

    public function testThumbnailAsBase64DataWithNullFileName(): void
    {
        $uploader = new ImageUploader($this->prophesize(Filesystem::class)->reveal(), '', $this->prophesize(LoggerInterface::class)->reveal());

        $this->assertNull($uploader->thumbnailAsBase64Data(null));
    }

    public function testThumbnailAsBase64DataWithNonExistingFile(): void
    {
        $fileName = 'consequatur_autem\quidem.jpeg';
        $fs = $this->prophesize(Filesystem::class);
        $fs->exists($fileName)->shouldBeCalledOnce()->willReturn(false);
        $uploader = new ImageUploader($fs->reveal(), '', $this->prophesize(LoggerInterface::class)->reveal());

        $this->assertNull($uploader->thumbnailAsBase64Data($fileName));
    }

    public function testThumbnailAsBase64DataWithExistingFile(): void
    {
        $fileName = 'quidem.jpeg';
        $content = '48161B11';

        $root = vfsStream::setup('test');
        $file = vfsStream::newFile($fileName)
            ->withContent($content)
            ->at($root);

        $uploader = new ImageUploader(new Filesystem(), vfsStream::url('test') . '/', $this->prophesize(LoggerInterface::class)->reveal());

        $this->assertEquals(base64_encode($content), $uploader->thumbnailAsBase64Data($file->getName()));
    }
}
