<?php

/*
 * This file is part of MediaVorus.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MediaVorus;

use MediaVorus\MediaCollection;
use PHPExiftool\Reader;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class MediaVorus
{

    public function __construct()
    {
        static $guesser_registered = false;

        if ( ! $guesser_registered) {
            $guesser = MimeTypeGuesser::getInstance();

            $guesser->register(new Utils\RawImageMimeTypeGuesser());
            $guesser->register(new Utils\PostScriptMimeTypeGuesser());
            $guesser->register(new Utils\AudioMimeTypeGuesser());
            $guesser->register(new Utils\VideoMimeTypeGuesser());

            $guesser_registered = true;
        }
    }

    /**
     * Build a Media Object given a file
     *
     * @param \SplFileInfo $file
     * @return \MediaVorus\Media\Media
     * @throws Exception\FileNotFoundException
     */
    public function guess(\SplFileInfo $file)
    {
        if ( ! $file instanceof File) {
            $file = new File($file->getPathname());
        }

        $classname = $this->guessFromMimeType($file->getMimeType());

        return new $classname($file);
    }

    /**
     *
     * @param \SplFileInfo $dir
     * @param type $recursive
     *
     * @return MediaCollection
     */
    public function inspectDirectory(\SplFileInfo $dir, $recursive = false)
    {
        $reader = new Reader();

        $reader->in($dir->getPathname())->followSymLinks();

        if ( ! $recursive) {
            $reader->notRecursive();
        }

        return $this->parseReaderResult($reader);
    }

    public function parseReaderResult(Reader $reader)
    {
        $files = new MediaCollection();

        foreach ($reader as $entity) {
            $file = new File($entity->getFile());
            $classname = $this->guessFromMimeType($file->getMimeType());
            $files[] = new $classname($file, $entity);
        }

        unset($reader);

        return $files;
    }

    /**
     * Return the corresponding \MediaVorus\Media\* class corresponding to a
     * mimetype
     *
     * @param string $mime
     * @return string The name of the MediaType class to use
     */
    protected function guessFromMimeType($mime)
    {
        $mime = strtolower($mime);

        switch (true) {
            case strpos($mime, 'image/') === 0:
            case $mime === 'application/postscript':
            case $mime === 'application/illustrator':
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
            case strpos($mime, 'text/*') === 0:
            case $mime === 'application/msword':
            case $mime === 'application/access':
            case $mime === 'application/pdf':
            case $mime === 'application/excel':
            case $mime === 'application/msword':
            case $mime === 'application/powerpoint':
            case $mime === 'application/vnd.ms-powerpoint':
            case $mime === 'application/vnd.ms-excel':
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
