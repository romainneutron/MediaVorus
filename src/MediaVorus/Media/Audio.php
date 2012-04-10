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

class Audio extends DefaultMedia
{

    /**
     *
     * @return string
     */
    public function getType()
    {
        return 'Audio';
    }

    /**
     * Get the duration of the audio in seconds, null if unavailable
     *
     * @return float
     */
    public function getDuration()
    {
        $sources = array('Composite:Duration');

        if (null !== $value = $this->findInSources($sources))
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

}
