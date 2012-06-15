<?php

namespace MediaVorus\Media;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Image
     */
    protected $object;
    protected $mediavorus;

    protected function setUp()
    {
        $this->object = new Image(new \SplFileInfo(__DIR__ . '/../../../files/ExifTool.jpg'));
        $this->mediavorus = new \MediaVorus\MediaVorus();
    }

    /**
     * @covers \MediaVorus\Media\Image::getType
     */
    public function testGetType()
    {
        $this->assertEquals(Media::TYPE_IMAGE, $this->object->getType());
    }

    /**
     * @covers \MediaVorus\Media\Image::isRawImage
     */
    public function testIsrawImage()
    {
        $this->assertFalse($this->object->isRawImage());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/CanonRaw.cr2'));
        $this->assertTrue($object->isRawImage());
    }

    /**
     * @covers \MediaVorus\Media\Image::getWidth
     * @covers \MediaVorus\Media\Image::extractFromDimensions
     */
    public function testGetWidth()
    {
        $this->assertTrue(is_int($this->object->getWidth()));
        $this->assertEquals(8, $this->object->getWidth());

        $objects = $this->mediavorus->inspectDirectory(new \SplFileInfo(__DIR__ . '/../../../../vendor/phpexiftool/exiftool/t/images/'));
        foreach ($objects as $object) {
            if ($object->getType() == Media::TYPE_IMAGE) {

                if (in_array($object->getFile()->getFilename(), array('KyoceraRaw.raw', 'Font.dfont', 'XMP.svg'))) {
                    $this->assertNull($object->getWidth());
                } else {
                    $this->assertTrue(is_int($object->getWidth()), $object->getFile()->getFilename() . " has int width");
                }
            }
        }
    }

    /**
     * @covers \MediaVorus\Media\Image::getHeight
     * @covers \MediaVorus\Media\Image::extractFromDimensions
     */
    public function testGetHeight()
    {
        $this->assertTrue(is_int($this->object->getHeight()));
        $this->assertEquals(8, $this->object->getHeight());

        $objects = $this->mediavorus->inspectDirectory(new \SplFileInfo(__DIR__ . '/../../../../vendor/phpexiftool/exiftool/t/images/'));
        foreach ($objects as $object) {
            if ($object->getType() == Media::TYPE_IMAGE) {

                if (in_array($object->getFile()->getFilename(), array('KyoceraRaw.raw', 'Font.dfont', 'XMP.svg'))) {
                    $this->assertNull($object->getHeight());
                } else {
                    $this->assertTrue(is_int($object->getHeight()), $object->getFile()->getFilename() . " has int width");
                }
            }
        }
    }

    /**
     * @covers \MediaVorus\Media\Image::getChannels
     */
    public function testGetChannels()
    {
        $this->assertTrue(is_int($this->object->getChannels()));
        $this->assertEquals(3, $this->object->getChannels());
    }

    /**
     * @covers \MediaVorus\Media\Image::getFocalLength
     */
    public function testGetFocalLength()
    {
        $this->assertTrue(is_float($this->object->getFocalLength()));
        $this->assertEquals(6.0, $this->object->getFocalLength());
    }

    /**
     * @covers \MediaVorus\Media\Image::getColorDepth
     */
    public function testGetColorDepth()
    {
        $this->assertTrue(is_int($this->object->getColorDepth()));
        $this->assertEquals(8, $this->object->getColorDepth());
    }

    /**
     * @covers \MediaVorus\Media\Image::getCameraModel
     */
    public function testGetCameraModel()
    {
        $this->assertTrue(is_string($this->object->getCameraModel()));
    }

    /**
     * @covers \MediaVorus\Media\Image::getFlashFired
     */
    public function testGetFlashFired()
    {
        $this->assertTrue(is_bool($this->object->getFlashFired()));

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/photo01.JPG'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertFalse($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/CanonRaw.cr2'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertFalse($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/photoAutoNoFlash.jpg'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertFalse($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/PhotoFlash.jpg'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertTrue($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/videoFlashed.MOV'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertNull($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../../vendor/phpexiftool/exiftool/t/images/XMP.xmp'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertFalse($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../../vendor/phpexiftool/exiftool/t/images/DNG.dng'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertFalse($object->getFlashFired());

        $object = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../../vendor/phpexiftool/exiftool/t/images/Panasonic.rw2'));
        $this->assertInstanceOf('\MediaVorus\Media\Image', $object);
        $this->assertFalse($object->getFlashFired());
    }

    /**
     * @covers \MediaVorus\Media\Image::getAperture
     */
    public function testGetAperture()
    {
        $this->assertInternalType('float', $this->object->getAperture());
    }

    /**
     * @covers \MediaVorus\Media\Image::getShutterSpeed
     */
    public function testGetShutterSpeed()
    {
        $this->assertInternalType('float', $this->object->getShutterSpeed());
    }

    /**
     * @covers \MediaVorus\Media\Image::getOrientation
     */
    public function testGetOrientation()
    {
        $object1 = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/photo01.JPG'));
        $object2 = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/photo02.JPG'));
        $object3 = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/photo03.JPG'));
        $object4 = $this->mediavorus->guess(new \SplFileInfo(__DIR__ . '/../../../files/Test.ogv'));

        $this->assertEquals(Image::ORIENTATION_0, $this->object->getOrientation());
        $this->assertEquals(Image::ORIENTATION_90, $object1->getOrientation());
        $this->assertEquals(Image::ORIENTATION_180, $object2->getOrientation());
        $this->assertEquals(Image::ORIENTATION_270, $object3->getOrientation());
        $this->assertNull($object4->getOrientation());
    }

    /**
     * @covers \MediaVorus\Media\Image::getCreationDate
     */
    public function testGetCreationDate()
    {
        $this->assertTrue(is_string($this->object->getCreationDate()));
    }

    /**
     * @covers \MediaVorus\Media\Image::getHyperfocalDistance
     */
    public function testGetHyperfocalDistance()
    {
        $this->assertInternalType('float', $this->object->getHyperfocalDistance());
    }

    /**
     * @covers \MediaVorus\Media\Image::getISO
     */
    public function testGetISO()
    {
        $this->assertTrue(is_int($this->object->getISO()));
        $this->assertEquals(100, $this->object->getISO());
    }

    /**
     * @covers \MediaVorus\Media\Image::getLightValue
     */
    public function testGetLightValue()
    {
        $this->assertInternalType('float', $this->object->getLightValue());
    }

    /**
     * @covers \MediaVorus\Media\Image::getColorSpace
     */
    public function testGetColorSpace()
    {
        $media = new \MediaVorus\Media\Image(__DIR__ . '/../../../files/ExifTool.jpg');

        $this->assertEquals(\MediaVorus\Media\Image::COLORSPACE_RGB, $media->getColorSpace());

        $media = new \MediaVorus\Media\Image(__DIR__ . '/../../../files/GRAYSCALE.jpg');
        $this->assertEquals(Image::COLORSPACE_GRAYSCALE, $media->getColorSpace());

        $media = new \MediaVorus\Media\Image(__DIR__ . '/../../../files/RVB.jpg');
        $this->assertEquals(Image::COLORSPACE_RGB, $media->getColorSpace());
    }
}
