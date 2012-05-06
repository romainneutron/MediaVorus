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

/**
 *
 * @author      Romain Neutron - imprec@gmail.com
 * @license     http://opensource.org/licenses/MIT MIT
 */
interface Media
{
    const TYPE_AUDIO = 'Audio';
    const TYPE_IMAGE = 'Image';
    const TYPE_VIDEO = 'Video';
    const TYPE_FLASH = 'Flash';
    const TYPE_DOCUMENT = 'Document';

    public function getFile();

    public function getType();
}
