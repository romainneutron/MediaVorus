#MediaVorus

[![Build Status](https://secure.travis-ci.org/romainneutron/MediaVorus.png?branch=master)](http://travis-ci.org/romainneutron/MediaVorus)

A tiny lib for playing with MultiMedia Files

This lib is under heavy development !

MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

ex :

```php
<?php

$Media = \MediaVorus\Media::guess('myRawFile.cr2');

if($Media instanceof '\MediaVorus\Media\Image')
{

  echo $media->getWidth();

  echo $media->getHeight();

  echo $media->getChannels();

  echo $media->getFocalLength();

  echo $media->getColorDepth();

  echo $media->getCameraModel();

  echo $media->getFlashFired();

  assert($Media->isRaw());
}

```

