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

use \MediaVorus\Utils\RawImageMimeTypeGuesser;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * @todo refactor Meta resolver to an independant object
 */
class Image extends DefaultMedia
{

    /**
     * Orientation constant Horizontal (normal)
     */

    const ORIENTATION_0        = 0;
    /**
     * Orientation constant Vertical (90 CW)
     */
    const ORIENTATION_90       = 90;
    /**
     * Orientation constant Vertical (270 CW)
     */
    const ORIENTATION_270      = 270;
    /**
     * Orientation constant Horizontal (reversed)
     */
    const ORIENTATION_180      = 180;
    /**
     * Colorspace constant CMYK
     */
    const COLORSPACE_CMYK      = 'CMYK';
    /**
     * Colorspace constant RGB
     */
    const COLORSPACE_RGB       = 'RGB';
    /**
     * Colorspace constant sRGB
     */
    const COLORSPACE_SRGB      = 'sRGB';
    /**
     * Colorspace constant Grayscale
     */
    const COLORSPACE_GRAYSCALE = 'Grayscale';

    /**
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_IMAGE;
    }

    /**
     * Returns true if the document is a "Raw" image
     *
     * @return boolean
     */
    public function isRawImage()
    {
        return in_array($this->getFile()->getMimeType(), RawImageMimeTypeGuesser::$rawMimeTypes);
    }

    /**
     * Returns true if the document has multiple layers.
     * This method is supposed to be used to extract layer 0 with ImageMagick
     *
     * @return type
     */
    public function hasMultipleLayers()
    {
        return in_array($this->getFile()->getMimeType(), array(
            'image/tiff',
            'application/pdf',
            'image/psd',
            'image/vnd.adobe.photoshop',
            'image/photoshop',
            'image/ai',
            'image/illustrator',
            'image/vnd.adobe.illustrator'
          ));
    }

    /**
     * Return the width, null on error
     *
     * @return int
     */
    public function getWidth()
    {
        if ($this->getMetadatas()->containsKey('File:ImageWidth'))
        {
            return (int) $this->getMetadatas()->get('File:ImageWidth')->getValue()->asString();
        }

        if ($this->getMetadatas()->containsKey('Composite:ImageSize'))
        {
            $dimensions = $this->extractFromDimensions(
              $this->getMetadatas()->get('Composite:ImageSize')->getValue()->asString()
            );

            if ($dimensions)
            {
                return (int) $dimensions['width'];
            }
        }

        $sources = array('SubIFD:ImageWidth', 'IFD0:ImageWidth', 'ExifIFD:ExifImageWidth');

        return $this->castValue($this->findInSources($sources), 'int');
    }

    /**
     * Return the height, null on error
     *
     * @return int
     */
    public function getHeight()
    {
        if ($this->getMetadatas()->containsKey('File:ImageHeight'))
        {
            return (int) $this->getMetadatas()->get('File:ImageHeight')->getValue()->asString();
        }

        if ($this->getMetadatas()->containsKey('Composite:ImageSize'))
        {
            $dimensions = $this->extractFromDimensions(
              $this->getMetadatas()->get('Composite:ImageSize')->getValue()->asString()
            );

            if ($dimensions)
            {
                return (int) $dimensions['height'];
            }
        }

        $sources = array('SubIFD:ImageHeight', 'IFD0:ImageHeight', 'ExifIFD:ExifImageHeight');

        return $this->castValue($this->findInSources($sources), 'int');
    }

    /**
     * Return the number of channels (samples per pixel), null on error
     *
     * @return int
     */
    public function getChannels()
    {
        $sources = array('File:ColorComponents', 'IFD0:SamplesPerPixel');

        return $this->castValue($this->findInSources($sources), 'int');
    }

    /**
     * Return the focal length used by the camera, null on error
     *
     * @return string
     */
    public function getFocalLength()
    {
        $sources = array('ExifIFD:FocalLength', 'XMP-exif:FocalLength');

        return $this->findInSources($sources);
    }

    /**
     * Return the color depth (bits per sample), null on error
     *
     * @return int
     */
    public function getColorDepth()
    {
        $sources = array('File:BitsPerSample', 'IFD0:BitsPerSample');

        return $this->castValue($this->findInSources($sources), 'int');
    }

    /**
     * Return the camera model, null on error
     *
     * @return string
     */
    public function getCameraModel()
    {
        $sources = array('IFD0:Model', 'IFD0:UniqueCameraModel');

        return $this->findInSources($sources);
    }

    /**
     * Return true if the Flash has been fired, false if it has not been
     * fired, null if does not know
     *
     * @return boolean
     */
    public function getFlashFired()
    {
        if (null !== $value = strtolower($this->findInSources(array('ExifIFD:Flash'))))
        {
            switch (true)
            {
                case strpos($value, 'not fire') !== false:
                case strpos($value, 'no flash') !== false:
                case strpos($value, 'off,') === 0:
                    return false;
                    break;
                case strpos($value, 'fired') !== false:
                case strpos($value, 'on,') === 0:
                    return true;
                    break;
            }
        }

        if (null !== $value = strtolower($this->findInSources(array('XMP-exif:FlashFired'))))
        {
            switch (true)
            {
                case $value === 'true':
                    return true;
                    break;
                case $value === 'false':
                    return false;
                    break;
            }
        }

        return null;
    }

    /**
     * Get Aperture value
     *
     * @return float
     */
    public function getAperture()
    {

        return $this->findInSources(array('Composite:Aperture'));
    }

    /**
     * Get ShutterSpeed value
     *
     * @return string
     */
    public function getShutterSpeed()
    {

        return $this->findInSources(array('Composite:ShutterSpeed'));
    }

    /**
     * Returns one one the ORIENTATION_* constants, the degrees value of Orientation
     *
     * @return int
     */
    public function getOrientation()
    {
        if (null !== $orientation = strtolower($this->findInSources(array('IFD0:Orientation'))))
        {
            switch (true)
            {
                case strpos($orientation, '90 cw') !== false:
                    return self::ORIENTATION_90;
                    break;
                case strpos($orientation, '270 cw') !== false:
                    return self::ORIENTATION_270;
                    break;
                case strpos($orientation, 'horizontal (normal)') !== false:
                    return self::ORIENTATION_0;
                    break;
                case strpos($orientation, '180') !== false:
                    return self::ORIENTATION_180;
                    break;
            }
        }

        return null;
    }

    /**
     * Returns the Creation Date
     *
     * @todo rename in getDateTaken to avoid conflicts with the original file
     * properties, return a DateTime object
     *
     * @return string
     */
    public function getCreationDate()
    {
        $sources = array('IPTC:DateCreated', 'ExifIFD:DateTimeOriginal');

        return $this->findInSources($sources);
    }

    /**
     * Return the Hyperfocal Distance
     *
     * @return string
     */
    public function getHyperfocalDistance()
    {

        return $this->findInSources(array('Composite:HyperfocalDistance'));
    }

    /**
     * Return the ISO value
     *
     * @return int
     */
    public function getISO()
    {
        $sources = array('ExifIFD:ISO', 'IFD0:ISO');

        return $this->castValue($this->findInSources($sources), 'int');
    }

    /**
     * Return the Light Value
     *
     * @return float
     */
    public function getLightValue()
    {

        return $this->findInSources(array('Composite:LightValue'));
    }

    /**
     * Returns the colorspace as one of the COLORSPACE_* constants
     *
     * @return string
     */
    public function getColorSpace()
    {
        $regexp = '/.*:(colorspace|colormode|colorspacedata)/i';

        foreach ($this->getMetadatas()->filterKeysByRegExp($regexp) as $meta)
        {
            switch (strtolower(trim($meta->getValue()->asString())))
            {
                case 'cmyk':
                    return self::COLORSPACE_CMYK;
                    break;
                case 'srgb':
                    return self::COLORSPACE_SRGB;
                    break;
                case 'rgb':
                    return self::COLORSPACE_RGB;
                    break;
                case 'grayscale':
                    return self::COLORSPACE_GRAYSCALE;
                    break;
            }
        }

        return null;
    }

    /**
     * Extract the width and height from a widthXheight serialized value
     * Returns an array with width and height keys, null on error
     *
     * @param type $WidthXHeight
     * @return array
     */
    protected function extractFromDimensions($WidthXHeight)
    {
        $values = explode('x', strtolower($WidthXHeight));

        if (count($values) === 2 && ctype_digit($values[0]) && ctype_digit($values[1]))
        {
            return array('width'  => $values[0], 'height' => $values[1]);
        }

        return null;
    }

}
