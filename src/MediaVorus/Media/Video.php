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

    /**
     * Get the duration of the video in seconds, null if unavailable
     *
     * @return float
     */
    public function getDuration()
    {

        if ($this->getMetadatas()->containsKey('Composite:Duration'))
        {
            $value = $this->getMetadatas()->get('Composite:Duration')->getValue();
        }
        elseif ($this->getMetadatas()->containsKey('Flash:Duration'))
        {
            $value = $this->getMetadatas()->get('Flash:Duration')->getValue();
        }
        elseif ($this->getMetadatas()->containsKey('QuickTime:Duration'))
        {
            $value = $this->getMetadatas()->get('QuickTime:Duration')->getValue();
        }
        elseif ($this->getMetadatas()->containsKey('Real-PROP:Duration'))
        {
            $value = $this->getMetadatas()->get('Real-PROP:Duration')->getValue();
        }

        if ($value)
        {
            preg_match('/([0-9\.]+) s/', $value, $matches);

            if (count($matches) > 0)
            {
                return (float) $matches[1];
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

                return (float) $duration;
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
        if ($this->getMetadatas()->containsKey('RIFF:FrameRate'))
        {
            return $this->getMetadatas()->get('RIFF:FrameRate')->getValue();
        }
        if ($this->getMetadatas()->containsKey('RIFF:VideoFrameRate'))
        {
            return $this->getMetadatas()->get('RIFF:VideoFrameRate')->getValue();
        }
        if ($this->getMetadatas()->containsKey('Flash:FrameRate'))
        {
            return $this->getMetadatas()->get('Flash:FrameRate')->getValue();
        }
        if (null !== $Framerate = $this->getEntity()->executeQuery('Track1:VideoFrameRate'))
        {
            return $Framerate;
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
        if ($this->getMetadatas()->containsKey('RIFF:AudioSampleRate'))
        {
            return $this->getMetadatas()->get('RIFF:AudioSampleRate')->getValue();
        }
        if ($this->getMetadatas()->containsKey('Flash:AudioSampleRate'))
        {
            return $this->getMetadatas()->get('Flash:AudioSampleRate')->getValue();
        }
        if (null !== $AudioBitRate = $this->getEntity()->executeQuery('Track2:AudioSampleRate'))
        {
            return $AudioBitRate;
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
        if ($this->getMetadatas()->containsKey('RIFF:VideoCodec'))
        {
            return $this->getMetadatas()->get('RIFF:VideoCodec')->getValue();
        }
        if ($this->getMetadatas()->containsKey('Flash:VideoEncoding'))
        {
            return $this->getMetadatas()->get('Flash:VideoEncoding')->getValue();
        }
        if (null !== $VideoCodec = $this->getEntity()->executeQuery('QuickTime:ComAppleProappsOriginalFormat'))
        {
            return $VideoCodec;
        }
        if (null !== $VideoCodec = $this->getEntity()->executeQuery('Track1:CompressorName'))
        {
            return $VideoCodec;
        }
        if (null !== $VideoCodec = $this->getEntity()->executeQuery('Track1:CompressorID'))
        {
            return $VideoCodec;
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
          && $this->getMetadatas()->get('RIFF:AudioCodec')->getValue() === '')
        {
            return $this->getMetadatas()->get('RIFF:Encoding')->getValue();
        }
        if ($this->getMetadatas()->containsKey('Flash:AudioEncoding'))
        {
            return $this->getMetadatas()->get('Flash:AudioEncoding')->getValue();
        }
        if (null !== $VideoCodec = $this->getEntity()->executeQuery('Track2:AudioFormat'))
        {
            return $VideoCodec;
        }

        return null;
    }

}
