<?php

namespace MediaVorus;

class FileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers MediaVorus\File::__construct
     * @covers MediaVorus\File::getMimeType
     */
    public function testGetMimeType()
    {
        $object = new File(__DIR__ . '/../../files/CanonRaw.cr2');
        $this->assertEquals('image/x-tika-canon', $object->getMimeType());
    }
    /**
     * @covers MediaVorus\File::__construct
     * @covers MediaVorus\Exception\Exception
     * @covers MediaVorus\Exception\FileNotFoundException
     * @expectedException MediaVorus\Exception\FileNotFoundException
     */
    public function testFileNotFound()
    {
        new File(__DIR__ . '/../../files/nonExistentFile');
    }
}
