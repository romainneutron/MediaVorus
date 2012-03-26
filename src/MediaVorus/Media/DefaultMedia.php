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
class DefaultMedia
{

  const GPSREF_LONGITUDE_WEST = 'W';
  const GPSREF_LONGITUDE_EAST = 'E';
  const GPSREF_LATITUDE_NORTH = 'N';
  const GPSREF_LATITUDE_SOUTH = 'S';

  /**
   *
   * @var \MediaVorus\File
   */
  protected $file;

  /**
   *
   * @var \PHPExiftool\Exiftool
   */
  protected $exiftool;

  /**
   *
   * @var \PHPExiftool\FileEntity
   */
  protected $entity;

  /**
   * Constructor for Medias
   *
   * @param \SplFileInfo $file
   * @param \PHPExiftool\Exiftool $exiftool
   * @return \MediaVorus\Media\DefaultMedia
   */
  public function __construct(\SplFileInfo $file, \PHPExiftool\Exiftool $exiftool, \PHPExiftool\FileEntity $entity = null)
  {
    if (!$file instanceof \MediaVorus\File)
    {
      $file = new \MediaVorus\File($file->getPathname());
    }

    $this->file = $file;
    $this->exiftool = $exiftool;
    $this->entity = $entity;

    return $this;
  }

  /**
   *
   * @return \MediaVorus\File
   */
  public function getFile()
  {
    return $this->file;
  }

  /**
   * Get Longitude value
   *
   * @return string
   */
  public function getLongitude()
  {
    if ($this->getMetadatas()->containsKey('GPS:GPSLongitude'))
    {
      return $this->getMetadatas()->get('GPS:GPSLongitude')->getValue();
    }
    if ($this->getMetadatas()->containsKey('Composite:GPSLongitude'))
    {
      $datas = $this->GPSCompositeExtract(
        $this->getMetadatas()->get('Composite:GPSLongitude')->getValue()
      );

      if ($datas)
      {
        return $datas['value'];
      }
    }

    return null;
  }

  /**
   * Get Longitude Reference value, one of the GPSREF_LONGITUDE_*
   *
   * @return string|null
   */
  public function getLongitudeRef()
  {
    if ($this->getMetadatas()->containsKey('GPS:GPSLongitudeRef'))
    {
      switch (strtolower($this->getMetadatas()->get('GPS:GPSLongitudeRef')->getValue()))
      {
        case 'west':
          return self::GPSREF_LONGITUDE_WEST;
          break;
        case 'east':
          return self::GPSREF_LONGITUDE_EAST;
          break;
      }
    }
    if ($this->getMetadatas()->containsKey('Composite:GPSLongitude'))
    {
      $datas = $this->GPSCompositeExtract(
        $this->getMetadatas()->get('Composite:GPSLongitude')->getValue()
      );

      if ($datas)
      {
        return $datas['ref'];
      }
    }

    return null;
  }

  /**
   * Get Latitude value
   *
   * @return string
   */
  public function getLatitude()
  {
    if ($this->getMetadatas()->containsKey('GPS:GPSLatitude'))
    {
      return $this->getMetadatas()->get('GPS:GPSLatitude')->getValue();
    }
    if ($this->getMetadatas()->containsKey('Composite:GPSLatitude'))
    {
      $datas = $this->GPSCompositeExtract(
        $this->getMetadatas()->get('Composite:GPSLatitude')->getValue()
      );

      if ($datas)
      {
        return $datas['value'];
      }
    }

    return null;
  }

  /**
   * Get Latitude Reference value, one of the GPSREF_LATITUDE_*
   *
   * @return string|null
   */
  public function getLatitudeRef()
  {
    if ($this->getMetadatas()->containsKey('GPS:GPSLatitudeRef'))
    {
      switch (strtolower($this->getMetadatas()->get('GPS:GPSLatitudeRef')->getValue()))
      {
        case 'north':
          return self::GPSREF_LATITUDE_NORTH;
          break;
        case 'south':
          return self::GPSREF_LATITUDE_SOUTH;
          break;
      }
    }
    if ($this->getMetadatas()->containsKey('Composite:GPSLatitude'))
    {
      $datas = $this->GPSCompositeExtract(
        $this->getMetadatas()->get('Composite:GPSLatitude')->getValue()
      );

      if ($datas)
      {
        return $datas['ref'];
      }
    }

    return null;
  }

  /**
   * Explode Coordinate and Reference in a concatenated string
   *
   * @param string $coordinate
   * @return array
   */
  protected function GPSCompositeExtract($coordinate)
  {
    $refs = array(
      self::GPSREF_LONGITUDE_EAST,
      self::GPSREF_LONGITUDE_WEST,
      self::GPSREF_LATITUDE_NORTH,
      self::GPSREF_LATITUDE_SOUTH,
    );
    $LatLong = implode('|', $refs);

    $pattern = '/([0-9]+\ deg [0-9]+\'\ [0-9\.]+")\ ([' . $LatLong . '])/';

    preg_match($pattern, $coordinate, $matches, 0);

    if (count($matches) === 3)
    {
      return array('value' => $matches[1], 'ref'   => $matches[2]);
    }

    return null;
  }

  /**
   *
   * @return \PHPExiftool\Driver\Metadata\MetadataBag
   */
  protected function getMetadatas()
  {

    return $this->getEntity()->getMetadatas();
  }

  protected function getEntity()
  {
    if (!$this->entity)
    {
      $this->entity = $this->exiftool->read($this->file);
    }

    return $this->entity;
  }

}
