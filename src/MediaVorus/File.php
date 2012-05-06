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

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class File extends SymfonyFile
{

    public function __construct($path)
    {
        try {
            parent::__construct($path, true);
        } catch (FileNotFoundException $e) {
            throw new Exception\FileNotFoundException(sprintf('File %s not found', $path));
        }
    }

    public function getMimeType()
    {
        $guesser = MimeTypeGuesser::getInstance();

        $guesser->register(new Utils\RawImageMimeTypeGuesser());
        $guesser->register(new Utils\VideoMimeTypeGuesser());

        return $guesser->guess($this->getPathname());
    }
}
