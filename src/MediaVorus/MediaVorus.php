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

namespace MediaVorus;

use MediaVorus\MediaCollection;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class MediaVorus
{

    /**
     * Build a Media Object given a file
     *
     * @param \SplFileInfo $file
     * @return \MediaVorus\Media\Media
     * @throws Exception\FileNotFoundException
     */
    public static function guess(\SplFileInfo $file)
    {
        if ( ! $file instanceof File)
        {
            try
            {
                $file = new File($file->getPathname(), true);
            }
            catch (FileNotFoundException $e)
            {
                throw new Exception\FileNotFoundException(sprintf('File %s not found', $file->getPathname()));
            }
        }

        $classname = static::guessFromMimeType($file->getMimeType());

        return new $classname($file, new \PHPExiftool\Exiftool());
    }

    /**
     *
     * @param \SplFileInfo $dir
     * @param type $recursive
     * @return MediaCollection
     */
    public static function inspectDirectory(\SplFileInfo $dir, $recursive = false)
    {
        $exiftool = new \PHPExiftool\Exiftool();

        $files = new MediaCollection();

        foreach ($entities = $exiftool->readDirectory($dir, $recursive) as $entity)
        {
            $file = new File($entity->getFile());

            $classname = static::guessFromMimeType($file->getMimeType());

            $files[] = new $classname($file, $exiftool, $entity);
        }

        return $files;
    }

    /**
     * Return the corresponding \MediaVorus\Media\* class corresponding to a
     * mimetype
     *
     * @param string $mime
     * @return string The name of the MediaType class to use
     */
    protected static function guessFromMimeType($mime)
    {
        switch (true)
        {
            case strpos($mime, 'image/') === 0:
            case 'application/postscript':
                return 'MediaVorus\Media\Image';
                break;

            case strpos($mime, 'video/') === 0:
            case 'application/vnd.rn-realmedia':
                return 'MediaVorus\Media\Video';
                break;

            /**
             * @todo Implements Audio
             */
            case strpos($mime, 'audio/') === 0:
                break;

            /**
             * @todo Implements Documents
             */
            case 'text/plain':
            case 'application/msword':
            case 'application/access':
            case 'application/pdf':
            case 'application/excel':
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.oasis.opendocument.formula':
            case 'application/vnd.oasis.opendocument.text-master':
            case 'application/vnd.oasis.opendocument.database':
            case 'application/vnd.oasis.opendocument.formula':
            case 'application/vnd.oasis.opendocument.chart':
            case 'application/vnd.oasis.opendocument.graphics':
            case 'application/vnd.oasis.opendocument.presentation':
            case 'application/vnd.oasis.opendocument.speadsheet':
            case 'application/vnd.oasis.opendocument.text':
                break;

            /**
             * @todo Implements Flash
             */
            case 'application/x-shockwave-flash':
                break;

            default:
                break;
        }

        return 'MediaVorus\Media\DefaultMedia';
    }

}
