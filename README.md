#MediaVorus

[![Build Status](https://secure.travis-ci.org/romainneutron/MediaVorus.png?branch=master)](http://travis-ci.org/romainneutron/MediaVorus)

A tiny lib for playing with MultiMedia Files

This lib is under heavy development !

MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

ex :

```php
<?php

$Media = \MediaVorus\Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

if($Media instanceof \MediaVorus\Media\Image)
{

  echo $Media->getWidth();

  echo $Media->getHeight();

  echo $Media->getChannels();

  echo $Media->getFocalLength();

  echo $Media->getColorDepth();

  echo $Media->getCameraModel();

  echo $Media->getFlashFired();

}

```

