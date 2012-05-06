<?php

namespace MediaVorus\Media;

class DefaultMediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultMedia
     */
    protected $object;
    protected $GPSobject;

    protected function setUp()
    {
        $this->object = new DefaultMedia(new \SplFileInfo(__DIR__ . '/../../../files/ExifTool.jpg'));
        $this->GPSobject = new DefaultMedia(new \SplFileInfo(__DIR__ . '/../../../files/GPS.jpg'));
    }

    /**
     * @covers \MediaVorus\Media\DefaultMedia::getFile
     */
    public function testGetFile()
    {
        $this->assertInstanceOf('\MediaVorus\File', $this->object->getFile());
        $this->assertEquals('ExifTool.jpg', $this->object->getFile()->getFilename());
    }

    /**
     * @covers \MediaVorus\Media\DefaultMedia::getLongitude
     */
    public function testGetLongitude()
    {
        $this->assertInternalType('float', $this->GPSobject->getLongitude());
        $this->assertEquals(1.91416666666667, $this->GPSobject->getLongitude());
    }

    /**
     * @covers \MediaVorus\Media\DefaultMedia::getLongitudeRef
     */
    public function testGetLongitudeRef()
    {
        $this->assertTrue(in_array($this->GPSobject->getLongitudeRef(), array('W', 'E')));
    }

    /**
     * @covers \MediaVorus\Media\DefaultMedia::getLatitude
     */
    public function testGetLatitude()
    {
        $this->assertInternalType('float', $this->GPSobject->getLatitude());
        $this->assertEquals(54.9896666666667, $this->GPSobject->getLatitude());
    }

    /**
     * @covers \MediaVorus\Media\DefaultMedia::getLatitudeRef
     */
    public function testGetLatitudeRef()
    {
        $this->assertTrue(in_array($this->GPSobject->getLatitudeRef(), array('N', 'S')));
    }
}

