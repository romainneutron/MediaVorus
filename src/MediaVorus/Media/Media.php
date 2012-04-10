<?php

namespace MediaVorus\Media;

interface Media
{

    const TYPE_AUDIO = 'Audio';
    const TYPE_IMAGE = 'Image';
    const TYPE_VIDEO = 'Video';

    public function getFile();

    public function getType();

}
