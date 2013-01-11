<?php

/*
 * This file is part of MediaVorus.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MediaVorus\Media;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\Exclude;
use FFMpeg\Exception\ExceptionInterface as FFMpegException;
use FFMpeg\FFProbe;
use MediaVorus\File;
use PHPExiftool\Writer;
use PHPExiftool\FileEntity;

/**
 * @ExclusionPolicy("all")
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class Video extends Image
{
    /**
     * @var FFProbe
     */
    protected $ffprobe;
    private $duration;
    private $width;
    private $height;

    public function __construct(File $file, FileEntity $entity, Writer $writer, FFProbe $ffprobe = null)
    {
        parent::__construct($file, $entity, $writer);
        $this->ffprobe = $ffprobe;
    }

    /**
     * @VirtualProperty
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_VIDEO;
    }

    /**
     * @VirtualProperty
     *
     * @return Integer
     */
    public function getWidth()
    {
        if ($this->width) {
            return $this->width;
        }

        if (null !== $result = parent::getWidth()) {
            return $result;
        }

        if ($this->ffprobe) {
            try {
                $data = json_decode($this->ffprobe->probeStreams($this->file->getPathname()), true);
            } catch (FFMpegException $e) {
                $data = array();
            }
        } else {
            $data = array();
        }

        foreach ($data as $stream) {
            foreach ($stream as $key => $value) {
                if ($key == 'width') {
                    return $this->width = (int) $value;
                }
            }
        }

        return null;
    }

    /**
     * @VirtualProperty
     *
     * @return Integer
     */
    public function getHeight()
    {
        if ($this->height) {
            return $this->height;
        }

        if (null !== $result = parent::getHeight()) {
            return $result;
        }

        if ($this->ffprobe) {
            try {
                $data = json_decode($this->ffprobe->probeStreams($this->file->getPathname()), true);
            } catch (FFMpegException $e) {
                $data = array();
            }
        } else {
            $data = array();
        }

        foreach ($data as $stream) {
            foreach ($stream as $key => $value) {
                if ($key == 'height') {
                    return $this->height = (int) $value;
                }
            }
        }

        return null;
    }

    /**
     * Get the duration of the video in seconds, null if unavailable
     *
     * @VirtualProperty
     *
     * @return float
     */
    public function getDuration()
    {
        if ($this->duration) {
            return $this->duration;
        }

        $sources = array('Composite:Duration', 'Flash:Duration', 'QuickTime:Duration', 'Real-PROP:Duration');

        if (null !== $value = $this->findInSources($sources)) {
            return (float) $value;
        }

        if ($this->ffprobe) {
            try {
                $result = json_decode($this->ffprobe->probeFormat($this->file->getPathname()), true);
            } catch (FFMpegException $e) {
                $result = array();
            }
        } else {
            $result = array();
        }

        foreach ($result as $key => $value) {
            if ($key == 'duration') {
                return $this->duration = (float) $value;
            }
        }

        return null;
    }

    /**
     * Returns the value of video frame rate, null if not available
     *
     * @VirtualProperty
     *
     * @return string
     */
    public function getFrameRate()
    {
        $sources = array('RIFF:FrameRate', 'RIFF:VideoFrameRate', 'Flash:FrameRate');

        if (null !== $value = $this->findInSources($sources)) {
            return $this->castValue($value, 'float');
        }

        if (null !== $value = $this->entity->executeQuery('Track1:VideoFrameRate')) {
            return $this->castValue($value->asString(), 'float');
        }

        if (null !== $value = $this->entity->executeQuery('Track2:VideoFrameRate')) {
            return $this->castValue($value->asString(), 'float');
        }

        return null;
    }

    /**
     * Returns the value of audio samplerate, null if not available
     *
     * @VirtualProperty
     *
     * @return string
     */
    public function getAudioSampleRate()
    {
        $sources = array('RIFF:AudioSampleRate', 'Flash:AudioSampleRate');

        if (null !== $value = $this->findInSources($sources)) {
            return $this->castValue($value->asString(), 'int');
        }

        if (null !== $value = $this->entity->executeQuery('Track1:AudioSampleRate')) {
            return $this->castValue($value->asString(), 'int');
        }

        if (null !== $value = $this->entity->executeQuery('Track2:AudioSampleRate')) {
            return $this->castValue($value->asString(), 'int');
        }

        return null;
    }

    /**
     * Returns the name of video codec, null if not available
     *
     * @VirtualProperty
     *
     * @return string
     */
    public function getVideoCodec()
    {
        $sources = array('RIFF:AudioSampleRate', 'Flash:VideoEncoding');

        if (null !== $value = $this->findInSources($sources)) {
            return $value;
        }

        if (null !== $value = $this->entity->executeQuery('QuickTime:ComAppleProappsOriginalFormat')) {
            return $this->castValue($value->asString(), 'string');
        }

        if (null !== $value = $this->entity->executeQuery('Track1:CompressorName')) {
            return $this->castValue($value->asString(), 'string');
        }

        if (null !== $value = $this->entity->executeQuery('Track2:CompressorName')) {
            return $this->castValue($value->asString(), 'string');
        }

        if (null !== $value = $this->entity->executeQuery('Track1:CompressorID')) {
            return $this->castValue($value->asString(), 'string');
        }

        if (null !== $value = $this->entity->executeQuery('Track2:CompressorID')) {
            return $this->castValue($value->asString(), 'string');
        }

        return null;
    }

    /**
     * Returns the name of audio codec, null if not available
     *
     * @VirtualProperty
     *
     * @return string
     */
    public function getAudioCodec()
    {
        if ($this->getMetadatas()->containsKey('RIFF:AudioCodec')
            && $this->getMetadatas()->containsKey('RIFF:Encoding')
            && $this->getMetadatas()->get('RIFF:AudioCodec')->getValue()->asString() === '') {
            return $this->getMetadatas()->get('RIFF:Encoding')->getValue()->asString();
        }

        if (null !== $value = $this->findInSources(array('Flash:AudioEncoding'))) {
            return $this->castValue($value, 'string');
        }

        if (null !== $VideoCodec = $this->entity->executeQuery('Track1:AudioFormat')) {
            return $this->castValue($VideoCodec->asString(), 'string');
        }

        if (null !== $VideoCodec = $this->entity->executeQuery('Track2:AudioFormat')) {
            return $this->castValue($VideoCodec->asString(), 'string');
        }

        return null;
    }
}
