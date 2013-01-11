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
use MediaVorus\File;
use MediaVorus\Exception\InvalidArgumentException;
use PHPExiftool\Driver\Metadata\MetadataBag;
use PHPExiftool\Exception\ExceptionInterface as PHPExiftoolExceptionInterface;
use PHPExiftool\Writer;
use PHPExiftool\FileEntity;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * @ExclusionPolicy("all")
 * @todo declarations of custom filters/getters
 */
class DefaultMedia implements MediaInterface
{
    const GPSREF_LONGITUDE_WEST = 'W';
    const GPSREF_LONGITUDE_EAST = 'E';
    const GPSREF_LATITUDE_NORTH = 'N';
    const GPSREF_LATITUDE_SOUTH = 'S';

    /**
     * @var File
     */
    protected $file;

    /**
     * @var FileEntity
     */
    protected $entity;

    /**
     * @var Writer
     */
    protected $writer;
    /**
     * @var type
     */
    protected $temporaryFiles = array();

    /**
     * Constructor for Medias
     *
     * @param File $file
     * @param FileEntity $entity
     * @return MediaInterface
     */
    public function __construct(File $file, FileEntity $entity, Writer $writer)
    {
        $this->file = $file;
        $this->entity = $entity;
        $this->writer = $writer;

        return $this;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        foreach ($this->temporaryFiles as $file) {
            $this->removeTemporaryFile($file);
        }

        $this->file = $this->entity = $this->temporaryFiles = null;
    }

    /**
     * Return the hash of the empty file (without metadatas)
     *
     * @param   string  $algo   The algorithm to use, available ones are returned by hash_algos()
     * @return  string  The computed hash
     *
     * @throws  InvalidArgumentException If the algorithm is not supported
     */
    public function getHash($algo)
    {
        if ( ! in_array($algo, hash_algos())) {
            throw new InvalidArgumentException(sprintf('Hash %s not supported', $algo));
        }

        $tmpFile = $this->getTemporaryEmptyFile();

        $hash = hash_file($algo, $tmpFile);

        $this->removeTemporaryFile($tmpFile);

        return $hash;
    }

    /**
     * @VirtualProperty
     *
     * @return string
     */
    public function getSha256()
    {
        return $this->getHash('sha256');
    }

    /**
     * @VirtualProperty
     *
     * @return string
     */
    public function getMd5()
    {
        return $this->getHash('md5');
    }

    /**
     * @VirtualProperty
     *
     * @return string
     */
    public function getSha1()
    {
        return $this->getHash('sha1');
    }

    /**
     * @VirtualProperty
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
     * @VirtualProperty
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
     * @VirtualProperty
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
     * @VirtualProperty
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
     * @VirtualProperty
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
     * @return MetadataBag
     */
    public function getMetadatas()
    {
        return $this->entity->getMetadatas();
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
            case 'integer':
                return (int) $value;
                break;
            case 'float':
                return (float) $value;
                break;
            case 'string':
                return (string) $value;
                break;
            default:
                return $value;
                break;
        }
    }

    /**
     * Generates a metadatas-free version of the file in the temporary directory
     *
     * @return string the path file to the temporary file
     */
    private function getTemporaryEmptyFile()
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'hash');

        unlink($tmpFile);

        try {
            $this->writer->reset();
            $this->writer->erase(true);
            $this->writer->write($this->file->getPathname(), new MetadataBag(), $tmpFile);

            $this->temporaryFiles[] = $tmpFile;
        } catch (PHPExiftoolExceptionInterface $e) {
            /**
             * Some files can not be written by exiftool
             */
            $tmpFile = $this->file->getPathname();
        }

        return $tmpFile;
    }

    /**
     * Remove a file generated by ::getTemporaryEmptyFile
     *
     * @param   String                          $pathfile The path to the file
     * @return  DefaultMedia
     */
    private function removeTemporaryFile($pathfile)
    {
        $temporayFiles = $this->temporaryFiles;

        foreach ($temporayFiles as $offset => $file) {
            if ($pathfile == $file && file_exists($file) && is_writable($file)) {
                unlink($pathfile);

                array_splice($this->temporaryFiles, $offset, 1);
            }
        }

        return $this;
    }
}
