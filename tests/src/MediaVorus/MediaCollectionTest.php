<?php

namespace MediaVorus;

class MediaCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers MediaVorus\MediaCollection::match
     */
    public function testMatch()
    {
        $mediavorus = new MediaVorus();
        $collection = $mediavorus->inspectDirectory(new \SplFileInfo(__DIR__ . '/../../'));

        $audio = $collection->match(new Filter\MediaType(Media\Media::TYPE_AUDIO));

        $this->assertInstanceOf('\\Doctrine\\Common\\Collections\\ArrayCollection', $audio);

        foreach ($audio as $audio) {
            $this->assertEquals(Media\Media::TYPE_AUDIO, $audio->getType());
        }

        $notAudio = $collection->match(new Filter\MediaType(Media\Media::TYPE_AUDIO), true);

        $this->assertInstanceOf('\\Doctrine\\Common\\Collections\\ArrayCollection', $notAudio);

        foreach ($notAudio as $audio) {
            $this->assertFalse(Media\Media::TYPE_AUDIO === $audio->getType());
        }
    }
}
