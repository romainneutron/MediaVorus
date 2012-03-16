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
class Image extends DefaultMedia
{

  /**
   * Return the width, null on error
   *
   * @return int
   */
  public function getWidth()
  {
    if ($this->getMetadatas()->containsKey('File:ImageWidth'))
    {
      return $this->getMetadatas()->get('File:ImageWidth')->getValue();
    }

    if ($this->getMetadatas()->containsKey('Composite:ImageSize'))
    {
      $dimensions = $this->extractFromDimensions(
        $this->getMetadatas()->get('Composite:ImageSize')->getValue()
      );

      if ($dimensions)
      {
        return $dimensions['width'];
      }
    }

    if ($this->getMetadatas()->containsKey('SubIFD:ImageWidth'))
    {
      return $this->getMetadatas()->get('ExifIFD:ExifImageWidth')->getValue();
    }

    if ($this->getMetadatas()->containsKey('IFD0:ImageWidth'))
    {
      return $this->getMetadatas()->get('ExifIFD:ExifImageWidth')->getValue();
    }

    if ($this->getMetadatas()->containsKey('ExifIFD:ExifImageWidth'))
    {
      return $this->getMetadatas()->get('ExifIFD:ExifImageWidth')->getValue();
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
      return $this->getMetadatas()->get('File:ImageHeight')->getValue();
    }

    if ($this->getMetadatas()->containsKey('Composite:ImageSize'))
    {
      $dimensions = $this->extractFromDimensions(
        $this->getMetadatas()->get('Composite:ImageSize')->getValue()
      );

      if ($dimensions)
      {
        return $dimensions['height'];
      }
    }

    if ($this->getMetadatas()->containsKey('SubIFD:ImageHeight'))
    {
      return $this->getMetadatas()->get('ExifIFD:ImageHeight')->getValue();
    }

    if ($this->getMetadatas()->containsKey('IFD0:ImageHeight'))
    {
      return $this->getMetadatas()->get('ExifIFD:ImageHeight')->getValue();
    }

    if ($this->getMetadatas()->containsKey('ExifIFD:ExifImageHeight'))
    {
      return $this->getMetadatas()->get('ExifIFD:ExifImageHeight')->getValue();
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
      return $this->getMetadatas()->get('File:ColorComponents')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:SamplesPerPixel'))
    {
      return $this->getMetadatas()->get('IFD0:SamplesPerPixel')->getValue();
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
      return $this->getMetadatas()->get('File:BitsPerSample')->getValue();
    }
    if ($this->getMetadatas()->containsKey('IFD0:BitsPerSample'))
    {
      return $this->getMetadatas()->get('IFD0:BitsPerSample')->getValue();
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
