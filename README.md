MediaVorus - A tiny PHP lib for playing with MultiMedia Files
=============================================================

[![Build Status](https://secure.travis-ci.org/romainneutron/MediaVorus.png?branch=master)](http://travis-ci.org/romainneutron/MediaVorus)

This lib is under heavy development !

#Exemple

```php
<?php

use MediaVorus\MediaVorus;
use MediaVorus\Media\Image;
use MediaVorus\Media\Video;

$mediavorus = MediaVorus::create();

$Image = $mediavorus->guess('RawCanon.cr2');

echo sprintf("File dimensions are %d x %d", $Image->getWidth(), $Image->getHeight());
echo sprintf("Photo as been taken at %s Shutter Speed", $Image->getShutterSpeed());

$Video = $mediavorus->guess('Movie.mpeg');

echo sprintf("Movie duration last %s s.", $Video->getDuration());
echo sprintf("Movie has been shot at longitude %s, latitude %s", $Video->getLongitude(), $Video->getLatitude());

```

#Documentation

Documentation is hosted on Read The Docs http://mediavorus.readthedocs.org/

#License

MediaVorus is released under MIT license http://opensource.org/licenses/MIT

