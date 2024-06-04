<?php

namespace App\UnitTests\Twig;

use App\Service\Domain\ImageUploader;
use App\Twig\MapExtension;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class MapExtensionTest extends TestCase
{
    use ProphecyTrait;

    #[TestWith([null, null])]
    #[TestWith(['41AA683A', 'nisi_odio\quidem.pptx'])]
    public function testThumbnailAsBase64Data(?string $expected, ?string $filename): void
    {
        $imageUploader = $this->prophesize(ImageUploader::class);
        $imageUploader->thumbnailAsBase64Data($filename)->shouldBeCalledOnce()->willReturn($expected);
        $extension = new MapExtension($imageUploader->reveal());
        $this->assertEquals($expected, $extension->thumbnailAsBase64Data($filename));
    }

    public function testGetFunctions(): void
    {
        $extension = new MapExtension($this->prophesize(ImageUploader::class)->reveal());
        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertSame('thumbnail', $functions[0]->getName());
        $this->assertEquals('thumbnailAsBase64Data', $functions[0]->getCallable()[1]);
    }
}
