<?php

namespace MediaVorus;

class MediaVorusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MediaVorus
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new MediaVorus;
    }

    /**
     * @covers MediaVorus\MediaVorus::guess
     */
    public function testGuess()
    {
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/ExifTool.jpg'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Media', $media);
    }

    /**
     * @covers MediaVorus\MediaVorus::guessFromMimeType
     */
    public function testGuessFromMimeType()
    {
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/ExifTool.jpg'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Image', $media);
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/CanonRaw.cr2'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Image', $media);
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/APE.ape'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Audio', $media);

        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/PDF.pdf'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Document', $media);
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/ZIP.gz'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\DefaultMedia', $media);
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/Flash.swf'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Flash', $media);
        $media = MediaVorus::guess(new \SplFileInfo(__DIR__ . '/../../files/Test.ogv'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Video', $media);
    }

    /**
     * @covers MediaVorus\MediaVorus::inspectDirectory
     */
    public function testInspectDirectory()
    {
        $medias = MediaVorus::inspectDirectory(new \SplFileInfo(__DIR__ . '/../../files'));
        $this->assertInstanceOf('\\MediaVorus\\MediaCollection', $medias);
        $this->assertEquals(16, count($medias));

        foreach ($medias as $media) {
            if ($media->getType() === Media\Media::TYPE_IMAGE) {
                $this->assertTrue(is_int($media->getWidth()));
            }
        }
    }
}
