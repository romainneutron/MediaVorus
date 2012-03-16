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

use \Symfony\Component\HttpFoundation\File\File as SymfonyFile;

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
class Media
{

  public static function guess(\SplFileInfo $file)
  {

    if (!$file instanceof SymfonyFile)
    {
      $file = new SymfonyFile($file->getPathname());
    }


    switch ($file->getMimeType())
    {

      case 'image/png':
      case 'image/gif':
      case 'image/bmp':
      case 'image/x-ms-bmp':
      case 'image/jpeg':
      case 'image/pjpeg':
      case 'image/psd':
      case 'image/photoshop':
      case 'image/vnd.adobe.photoshop':
      case 'image/ai':
      case 'image/illustrator':
      case 'image/vnd.adobe.illustrator':
      case 'image/tiff':
      case 'image/x-photoshop':
      case 'application/postscript':
      case 'image/x-tika-canon':
      case 'image/x-canon-cr2':
      case 'image/x-tika-casio':
      case 'image/x-tika-dng':
      case 'image/x-tika-epson':
      case 'image/x-tika-fuji':
      case 'image/x-tika-hasselblad':
      case 'image/x-tika-imacon':
      case 'image/x-tika-kodak':
      case 'image/x-tika-leaf':
      case 'image/x-tika-logitech':
      case 'image/x-tika-mamiya':
      case 'image/x-tika-minolta':
      case 'image/x-tika-nikon':
      case 'image/x-tika-olympus':
      case 'image/x-tika-panasonic':
      case 'image/x-tika-pentax':
      case 'image/x-tika-phaseone':
      case 'image/x-tika-rawzor':
      case 'image/x-tika-red':
      case 'image/x-tika-sigma':
      case 'image/x-tika-sony':
      case 'image/x-portable-pixmap':
        return new Media\Image($file, new \PHPExiftool\Exiftool);
        break;

      case 'video/mpeg':
      case 'video/mp4':
      case 'video/x-ms-wmv':
      case 'video/x-ms-wmx':
      case 'video/avi':
      case 'video/mp2p':
      case 'video/mp4':
      case 'video/x-ms-asf':
      case 'video/quicktime':
      case 'video/matroska':
      case 'video/x-msvideo':
      case 'video/x-ms-video':
      case 'video/x-flv':
      case 'video/avi':
      case 'video/3gpp':
      case 'video/x-m4v':
      case 'application/vnd.rn-realmedia':
        break;

      case 'audio/aiff':
      case 'audio/aiff':
      case 'audio/x-mpegurl':
      case 'audio/mid':
      case 'audio/mid':
      case 'audio/mpeg':
      case 'audio/ogg':
      case 'audio/mp4':
      case 'audio/scpls':
      case 'audio/vnd.rn-realaudio':
      case 'audio/x-pn-realaudio':
      case 'audio/wav':
      case 'audio/x-wav':
      case 'audio/x-ms-wma':
      case 'audio/x-flac':
        break;

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

      case 'application/x-shockwave-flash':
        break;

      default:
        break;
    }


    return new Media\DefaultMedia($file, new \PHPExiftool\Exiftool());
  }

}
