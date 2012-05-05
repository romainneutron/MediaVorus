<?php

/**
 * Copyright (c) 2012 Romain Neutron
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

namespace MediaVorus\Media;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class Video extends Image
{

    protected $ffprobe;
    protected $duration;

    public function __construct($file, FileEntity $entity = null)
    {
        parent::__construct($file, $entity);

        $this->ffprobe = \FFMpeg\FFProbe::load();
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_VIDEO;
    }

    /**
     * Get the duration of the video in seconds, null if unavailable
     *
     * @return float
     */
    public function getDuration()
    {
        if ($this->duration)
        {
            return $this->duration;
        }

        $sources = array('Composite:Duration', 'Flash:Duration', 'QuickTime:Duration', 'Real-PROP:Duration');

        if (null !== $value = $this->findInSources($sources))
        {
            preg_match('/([0-9\.]+) s/', $value, $matches);

            if (count($matches) > 0)
            {
                return $this->duration = (float) $matches[1];
            }

            preg_match('/[0-9]+:[0-9]+:[0-9\.]+/', $value, $matches);

            if (count($matches) > 0)
            {
                $data = explode(':', $matches[0]);

                $duration = 0;
                $factor   = 1;
                while ($segment  = array_pop($data))
                {
                    $duration += $segment * $factor;
                    $factor *=60;
                }

                return $this->duration = (float) $duration;
            }
        }


        $result = $this->ffprobe->probeFormat($this->file->getPathname());

        foreach (explode("\n", $result) as $line)
        {
            if (preg_match('/duration=([\d\.]+)/i', $line, $matches))
            {
                return $this->duration = (int) $matches[1];
            }
        }

        return null;
    }

    /**
     * Returns the value of video frame rate, null if not available
     *
     * @return string
     */
    public function getFrameRate()
    {
        $sources = array('RIFF:FrameRate', 'RIFF:VideoFrameRate', 'Flash:FrameRate');

        if (null !== $value = $this->findInSources($sources))
        {
            return $value;
        }

        if (null !== $value = $this->getEntity()->executeQuery('Track1:VideoFrameRate'))
        {
            return $value;
        }

        return null;
    }

    /**
     * Returns the value of audio samplerate, null if not available
     *
     * @return string
     */
    public function getAudioSampleRate()
    {
        $sources = array('RIFF:AudioSampleRate', 'Flash:AudioSampleRate');

        if (null !== $value = $this->findInSources($sources))
        {
            return $value;
        }

        if (null !== $value = $this->getEntity()->executeQuery('Track2:AudioSampleRate'))
        {
            return $value;
        }

        return null;
    }

    /**
     * Returns the name of video codec, null if not available
     *
     * @return string
     */
    public function getVideoCodec()
    {
        $sources = array('RIFF:AudioSampleRate', 'Flash:VideoEncoding');

        if (null !== $value = $this->findInSources($sources))
        {
            return $value;
        }

        if (null !== $value = $this->getEntity()->executeQuery('QuickTime:ComAppleProappsOriginalFormat'))
        {
            return $value;
        }
        if (null !== $value = $this->getEntity()->executeQuery('Track1:CompressorName'))
        {
            return $value;
        }
        if (null !== $value = $this->getEntity()->executeQuery('Track1:CompressorID'))
        {
            return $value;
        }

        return null;
    }

    /**
     * Returns the name of audio codec, null if not available
     *
     * @return string
     */
    public function getAudioCodec()
    {
        if ($this->getMetadatas()->containsKey('RIFF:AudioCodec')
          && $this->getMetadatas()->containsKey('RIFF:Encoding')
          && $this->getMetadatas()->get('RIFF:AudioCodec')->getValue()->asString() === '')
        {
            return $this->getMetadatas()->get('RIFF:Encoding')->getValue()->asString();
        }
        if ($this->getMetadatas()->containsKey('Flash:AudioEncoding'))
        {
            return $this->getMetadatas()->get('Flash:AudioEncoding')->getValue()->asString();
        }
        if (null !== $VideoCodec = $this->getEntity()->executeQuery('Track2:AudioFormat'))
        {
            return $VideoCodec;
        }

        return null;
    }

}
