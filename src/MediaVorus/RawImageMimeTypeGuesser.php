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

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class RawImageMimeTypeGuesser implements MimeTypeGuesserInterface
{

  public function guess($path)
  {
    $extension = strtolower(pathinfo(basename($path), PATHINFO_EXTENSION));

    $types = array(
      '3fr'  => 'image/x-tika-hasselblad',
      'arw'  => 'image/x-tika-sony',
      'bay'  => 'image/x-tika-casio',
      'cap'  => 'image/x-tika-phaseone',
      'cr2-' => 'image/x-canon-cr2',
      'cr2'  => 'image/x-tika-canon',
      'crw'  => 'image/x-tika-canon',
      'dcs'  => 'image/x-tika-kodak',
      'dcr'  => 'image/x-tika-kodak',
      'dng'  => 'image/x-tika-dng',
      'drf'  => 'image/x-tika-kodak',
      'erf'  => 'image/x-tika-epson',
      'fff'  => 'image/x-tika-imacon',
      'iiq'  => 'image/x-tika-phaseone',
      'kdc'  => 'image/x-tika-kodak',
      'k25'  => 'image/x-tika-kodak',
      'mef'  => 'image/x-tika-mamiya',
      'mos'  => 'image/x-tika-leaf',
      'mrw'  => 'image/x-tika-minolta',
      'nef'  => 'image/x-tika-nikon',
      'nrw'  => 'image/x-tika-nikon',
      'orf'  => 'image/x-tika-olympus',
      'pef'  => 'image/x-tika-pentax',
      'ppm'  => 'image/x-portable-pixmap',
      'ptx'  => 'image/x-tika-pentax',
      'pxn'  => 'image/x-tika-logitech',
      'raf'  => 'image/x-tika-fuji',
      'raw'  => 'image/x-tika-panasonic',
      'r3d'  => 'image/x-tika-red',
      'rw2'  => 'image/x-tika-panasonic',
      'rwz'  => 'image/x-tika-rawzor',
      'sr2'  => 'image/x-tika-sony',
      'srf'  => 'image/x-tika-sony',
      'x3f'  => 'image/x-tika-sigma',
    );

    if (array_key_exists($extension, $types))
    {
      return $types[$extension];
    }

    return null;
  }

}
