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

use PHPExiftool\FileEntity;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class DefaultMedia implements Media
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
     * @var \PHPExiftool\FileEntity
     */
    protected $entity;

    /**
     * Constructor for Medias
     *
     * @param string|\SplFileInfo|\MediaVorus\File $file
     * @param \PHPExiftool\Exiftool $exiftool
     * @return \MediaVorus\Media\DefaultMedia
     */
    public function __construct($file, FileEntity $entity = null)
    {
        switch (true) {
            case ! is_object($file):
                $file = new \MediaVorus\File($file);
                break;
            case $file instanceof \MediaVorus\File:
                break;
            case $file instanceof \SplFileInfo:
                $file = new \MediaVorus\File($file->getPathname());
                break;
            default:
                throw new \MediaVorus\InvalidArgumentException('$file should be either a pathname, a \SplFileInfo or \MediaVorus\File');
                break;
        }

        $this->file = $file;
        $this->entity = $entity;

        return $this;
    }

    public function getHash($algo)
    {
        if ( ! in_array($algo, hash_algos())) {
            throw new \MediaVorus\Exception\InvalidArgumentException('Requested hash not supported');
        }

        return hash_file($algo, $this->file->getPathname());
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return 'DefaultMedia';
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
     * @return float
     */
    public function getLongitude()
    {
        if ($this->getMetadatas()->containsKey('GPS:GPSLongitude')) {
            return (float) $this->getMetadatas()->get('GPS:GPSLongitude')->getValue()->asString();
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
        if ($this->getMetadatas()->containsKey('GPS:GPSLongitudeRef')) {
            switch (strtolower($this->getMetadatas()->get('GPS:GPSLongitudeRef')->getValue()->asString())) {
                case 'w':
                    return self::GPSREF_LONGITUDE_WEST;
                    break;
                case 'e':
                    return self::GPSREF_LONGITUDE_EAST;
                    break;
            }
        }

        return null;
    }

    /**
     * Get Latitude value
     *
     * @return float
     */
    public function getLatitude()
    {
        if ($this->getMetadatas()->containsKey('GPS:GPSLatitude')) {
            return (float) $this->getMetadatas()->get('GPS:GPSLatitude')->getValue()->asString();
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
        if ($this->getMetadatas()->containsKey('GPS:GPSLatitudeRef')) {
            switch (strtolower($this->getMetadatas()->get('GPS:GPSLatitudeRef')->getValue()->asString())) {
                case 'n':
                    return self::GPSREF_LATITUDE_NORTH;
                    break;
                case 's':
                    return self::GPSREF_LATITUDE_SOUTH;
                    break;
            }
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
        if ( ! $this->entity) {
            $reader = new \PHPExiftool\Reader();

            $this->entity = $reader->files($this->file->getPathname())->first();
        }

        return $this->entity;
    }

    protected function findInSources(Array $sources)
    {
        foreach ($sources as $source) {
            if ($this->getMetadatas()->containsKey($source)) {
                return $this->getMetadatas()->get($source)->getValue()->asString();
            }
        }

        return null;
    }

    protected function castValue($value, $type)
    {
        if (is_null($value)) {
            return null;
        }

        switch ($type) {
            case 'int':
                return (int) $value;
                break;
            case 'float':
                return (float) $value;
                break;
            default:
                return $value;
                break;
        }
    }
}
