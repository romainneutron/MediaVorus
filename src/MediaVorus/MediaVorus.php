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
use PHPExiftool\Reader;

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
        if ( ! $file instanceof File) {
            $file = new File($file->getPathname());
        }

        $classname = static::guessFromMimeType($file->getMimeType());

        return new $classname($file);
    }

    /**
     *
     * @todo take an exiftool reader as argument
     *
     * @param \SplFileInfo $dir
     * @param type $recursive
     * @return MediaCollection
     */
    public static function inspectDirectory(\SplFileInfo $dir, $recursive = false)
    {
        $reader = new Reader();

        $reader->in($dir->getPathname())->followSymLinks();

        if ( ! $recursive) {
            $reader->notRecursive();
        }

        $files = new MediaCollection();

        foreach ($reader as $entity) {
            $file = new File($entity->getFile());

            $classname = static::guessFromMimeType($file->getMimeType());

            $files[] = new $classname($file, $entity);
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
        $mime = strtolower($mime);

        switch (true) {
            case strpos($mime, 'image/') === 0:
            case $mime === 'application/postscript':
                return 'MediaVorus\Media\Image';
                break;

            case strpos($mime, 'video/') === 0:
            case $mime === 'application/vnd.rn-realmedia':
                return 'MediaVorus\Media\Video';
                break;

            /**
             * @todo Implements Audio
             */
            case strpos($mime, 'audio/') === 0:
                return 'MediaVorus\Media\Audio';
                break;

            /**
             * @todo Implements Documents
             */
            case $mime === 'text/plain':
            case $mime === 'application/msword':
            case $mime === 'application/access':
            case $mime === 'application/pdf':
            case $mime === 'application/excel':
            case $mime === 'application/vnd.ms-powerpoint':
            case $mime === 'application/vnd.oasis.opendocument.formula':
            case $mime === 'application/vnd.oasis.opendocument.text-master':
            case $mime === 'application/vnd.oasis.opendocument.database':
            case $mime === 'application/vnd.oasis.opendocument.formula':
            case $mime === 'application/vnd.oasis.opendocument.chart':
            case $mime === 'application/vnd.oasis.opendocument.graphics':
            case $mime === 'application/vnd.oasis.opendocument.presentation':
            case $mime === 'application/vnd.oasis.opendocument.speadsheet':
            case $mime === 'application/vnd.oasis.opendocument.text':
                return 'MediaVorus\Media\Document';
                break;

            /**
             * @todo Implements Flash
             */
            case $mime === 'application/x-shockwave-flash':
                return 'MediaVorus\Media\Flash';
                break;

            default:
                break;
        }

        return 'MediaVorus\Media\DefaultMedia';
    }
}
