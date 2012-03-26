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
 *
 * @todo refactor Meta resolver to an independant object
 */
class Image extends DefaultMedia
{

  /**
   * Orientation constant Horizontal (normal)
   */
  const ORIENTATION_0 = 'Horizontal';
  /**
   * Orientation constant Vertical (90 CW)
   */
  const ORIENTATION_90 = 'Vertical 90 CW';
  /**
   * Orientation constant Vertical (270 CW)
   */
  const ORIENTATION_270 = 'Vertical 270 CW';
  /**
   * Orientation constant Horizontal (reversed)
   */
  const ORIENTATION_180 = 'Reversed';

  /**
   * Return the width, null on error
   *
   * @return int
   */
  public function getWidth()
  {
    if ($this->getMetadatas()->containsKey('File:ImageWidth'))
    {
      return (int) $this->getMetadatas()->get('File:ImageWidth')->getValue();
    }

    if ($this->getMetadatas()->containsKey('Composite:ImageSize'))
    {
      $dimensions = $this->extractFromDimensions(
        $this->getMetadatas()->get('Composite:ImageSize')->getValue()
      );

      if ($dimensions)
      {
        return (int) $dimensions['width'];
      }
    }

    if ($this->getMetadatas()->containsKey('SubIFD:ImageWidth'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ExifImageWidth')->getValue();
    }

    if ($this->getMetadatas()->containsKey('IFD0:ImageWidth'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ExifImageWidth')->getValue();
    }

    if ($this->getMetadatas()->containsKey('ExifIFD:ExifImageWidth'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ExifImageWidth')->getValue();
    }

    return null;
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
      return (int) $this->getMetadatas()->get('File:ImageHeight')->getValue();
    }

    if ($this->getMetadatas()->containsKey('Composite:ImageSize'))
    {
      $dimensions = $this->extractFromDimensions(
        $this->getMetadatas()->get('Composite:ImageSize')->getValue()
      );

      if ($dimensions)
      {
        return (int) $dimensions['height'];
      }
    }

    if ($this->getMetadatas()->containsKey('SubIFD:ImageHeight'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ImageHeight')->getValue();
    }

    if ($this->getMetadatas()->containsKey('IFD0:ImageHeight'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ImageHeight')->getValue();
    }

    if ($this->getMetadatas()->containsKey('ExifIFD:ExifImageHeight'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ExifImageHeight')->getValue();
    }

    return null;
  }

  /**
   * Return the number of channels (samples per pixel), null on error
   *
   * @return int
   */
  public function getChannels()
  {
    if ($this->getMetadatas()->containsKey('File:ColorComponents'))
    {
      return (int) $this->getMetadatas()->get('File:ColorComponents')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:SamplesPerPixel'))
    {
      return (int) $this->getMetadatas()->get('IFD0:SamplesPerPixel')->getValue();
    }

    return null;
  }

  /**
   * Return the focal length used by the camera, null on error
   *
   * @return string
   */
  public function getFocalLength()
  {
    if ($this->getMetadatas()->containsKey('ExifIFD:FocalLength'))
    {
      return $this->getMetadatas()->get('ExifIFD:FocalLength')->getValue();
    }
    if ($this->getMetadatas()->containsKey('XMP-exif:FocalLength'))
    {
      return $this->getMetadatas()->get('XMP-exif:FocalLength')->getValue();
    }

    return null;
  }

  /**
   * Return the color depth (bits per sample), null on error
   *
   * @return int
   */
  public function getColorDepth()
  {
    if ($this->getMetadatas()->containsKey('File:BitsPerSample'))
    {
      return (int) $this->getMetadatas()->get('File:BitsPerSample')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:BitsPerSample'))
    {
      return (int) $this->getMetadatas()->get('IFD0:BitsPerSample')->getValue();
    }

    return null;
  }

  /**
   * Return the camera model, null on error
   *
   * @return string
   */
  public function getCameraModel()
  {
    if ($this->getMetadatas()->containsKey('IFD0:Model'))
    {
      return $this->getMetadatas()->get('IFD0:Model')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:UniqueCameraModel'))
    {
      return $this->getMetadatas()->get('IFD0:UniqueCameraModel')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:UniqueCameraModel'))
    {
      return $this->getMetadatas()->get('IFD0:UniqueCameraModel')->getValue();
    }

    return null;
  }

  /**
   * Return true if the Flash has been fired, false if it has not been
   * fired, null if does not know
   *
   * @return boolean
   */
  public function getFlashFired()
  {
    if ($this->getMetadatas()->containsKey('ExifIFD:Flash'))
    {
      $value = strtolower($this->getMetadatas()->get('ExifIFD:Flash')->getValue());
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

    if ($this->getMetadatas()->containsKey('XMP-exif:FlashFired'))
    {
      $value = strtolower($this->getMetadatas()->get('XMP-exif:FlashFired')->getValue());
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
    if ($this->getMetadatas()->containsKey('Composite:Aperture'))
    {
      return $this->getMetadatas()->get('Composite:Aperture')->getValue();
    }

    return null;
  }

  /**
   * Get ShutterSpeed value
   *
   * @return string
   */
  public function getShutterSpeed()
  {
    if ($this->getMetadatas()->containsKey('Composite:ShutterSpeed'))
    {
      return $this->getMetadatas()->get('Composite:ShutterSpeed')->getValue();
    }

    return null;
  }

  /**
   * Returns one one the ORIENTATION_* constants
   *
   * @return string
   */
  public function getOrientation()
  {
    if ($this->getMetadatas()->containsKey('IFD0:Orientation'))
    {
      $orientation = strtolower($this->getMetadatas()->get('IFD0:Orientation')->getValue());

      switch(true)
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
    if ($this->getMetadatas()->containsKey('IPTC:DateCreated'))
    {
      return $this->getMetadatas()->get('IPTC:DateCreated')->getValue();
    }
    if ($this->getMetadatas()->containsKey('ExifIFD:DateTimeOriginal'))
    {
      return $this->getMetadatas()->get('ExifIFD:DateTimeOriginal')->getValue();
    }

    return null;
  }

  /**
   * Return the Hyperfocal Distance
   *
   * @return string
   */
  public function getHyperfocalDistance()
  {
    if ($this->getMetadatas()->containsKey('Composite:HyperfocalDistance'))
    {
      return $this->getMetadatas()->get('Composite:HyperfocalDistance')->getValue();
    }

    return null;
  }

  /**
   * Return the ISO value
   *
   * @return int
   */
  public function getISO()
  {
    if ($this->getMetadatas()->containsKey('ExifIFD:ISO'))
    {
      return (int) $this->getMetadatas()->get('ExifIFD:ISO')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:ISO'))
    {
      return (int) $this->getMetadatas()->get('IFD0:ISO')->getValue();
    }

    return null;
  }

  /**
   * Return the Light Value
   *
   * @return float
   */
  public function getLightValue()
  {
    if ($this->getMetadatas()->containsKey('Composite:LightValue'))
    {
      return $this->getMetadatas()->get('Composite:LightValue')->getValue();
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
