<?php

namespace MediaVorus;

class MediaVorusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MediaVorus
     */
    protected $object;

    /**
     * @covers MediaVorus\MediaVorus::__construct
     */
    protected function setUp()
    {
        $this->object = new MediaVorus;
    }

    /**
     * @covers MediaVorus\MediaVorus::guess
     */
    public function testGuess()
    {
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/ExifTool.jpg'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Media', $media);
    }

    /**
     * @covers MediaVorus\MediaVorus::guessFromMimeType
     */
    public function testGuessFromMimeType()
    {
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/ExifTool.jpg'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Image', $media);
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/CanonRaw.cr2'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Image', $media);
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/APE.ape'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Audio', $media);

        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/PDF.pdf'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Document', $media);
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/ZIP.gz'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\DefaultMedia', $media);
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/Flash.swf'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Flash', $media);
        $media = $this->object->guess(new \SplFileInfo(__DIR__ . '/../../files/Test.ogv'));
        $this->assertInstanceOf('\\MediaVorus\\Media\\Video', $media);
    }

    /**
     * @covers MediaVorus\MediaVorus::inspectDirectory
     */
    public function testInspectDirectory()
    {
        $medias = $this->object->inspectDirectory(new \SplFileInfo(__DIR__ . '/../../files'));
        $this->assertInstanceOf('\\MediaVorus\\MediaCollection', $medias);
        $this->assertEquals(22, count($medias));

        foreach ($medias as $media) {
            if ($media->getType() === Media\Media::TYPE_IMAGE) {
		if (!in_array($media->getFile()->getFilename(), array('KyoceraRaw.raw', 'XMP.svg'))) {
	            $this->assertTrue(is_int($media->getWidth()));
		}
            }
        }
    }
}
