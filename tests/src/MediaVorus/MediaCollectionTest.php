<?php

namespace MediaVorus;

use FFMpeg\FFProbe;
use MediaVorus\Media\MediaInterface;
use MediaVorus\Filter\MediaType;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use PHPExiftool\Writer;
use PHPExiftool\Reader;

class MediaCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers MediaVorus\MediaCollection::match
     */
    public function testMatch()
    {
        $logger = new Logger('test');
        $logger->pushHandler(new NullHandler());

        $mediavorus = new MediaVorus(Reader::create(), Writer::create(), FFProbe::load($logger));

        $collection = $mediavorus->inspectDirectory(__DIR__ . '/../../');
        $audio = $collection->match(new MediaType(MediaInterface::TYPE_AUDIO));

        $this->assertInstanceOf('\\Doctrine\\Common\\Collections\\ArrayCollection', $audio);

        foreach ($audio as $audio) {
            $this->assertEquals(Media\Media::TYPE_AUDIO, $audio->getType());
        }

        $notAudio = $collection->match(new MediaType(MediaInterface::TYPE_AUDIO), true);

        $this->assertInstanceOf('\\Doctrine\\Common\\Collections\\ArrayCollection', $notAudio);

        foreach ($notAudio as $audio) {
            $this->assertFalse(Media\Media::TYPE_AUDIO === $audio->getType());
        }
    }
}
