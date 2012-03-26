MediaVorus - A tiny PHP lib for playing with MultiMedia Files
=============================================================

[![Build Status](https://secure.travis-ci.org/romainneutron/MediaVorus.png?branch=master)](http://travis-ci.org/romainneutron/MediaVorus)

This lib is under heavy development !

MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

Example
-------

```php
<?php

$Media = \MediaVorus\Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

if($Media instanceof \MediaVorus\Media\Image)
{
    echo sprintf("Found a file with dimensions : %dx%d", $Media->getWidth(), $Media->getHeight());
}

```

Goals
-----

This library is built on Symfony\HttpFoundation component for handling files
and PHPExiftool which is a PHP driver for Exiftool.
Doctrine Common Package is also required to take advantage of the powerful
ArrayCollection

The aim is to provide an abstract layer between the program and the multimedia
file.

First, the need is to analyze multimedia files and get their properties.

In a very next future, we would add metadata mapper to handle various
configurations.


API
===

Guesser
-------

```php
<?php

$Media = \MediaVorus\Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

//Returns a \Doctrine\Common\Collections\ArrayCollection of Medias
$MediaCollection = \MediaVorus\Media::inspectDirectory($dir, $recursive);

```


Medias:
-------


\MediaVorus\Media\DefaultMedia
------------------------------

Default Media is the Default container.
This object provides GPS informations :



```php
<?php

$Media = \MediaVorus\Media::guess(new \SplFileInfo('somefile.smf'));

if($Media instanceof \MediaVorus\Media\DefaultMedia)
{
    /**
     * Returns the longitude as a described above
     */
    $Media->getLongitude();
    /**
     * Returns the longitude as a described above
     */
    $Media->getLatitude();
    /**
     * Returns Longitude reference (West or East) and equals to one of the
     * \MediaVorus\Media\DefaultMedia::GPSREF_LONGITUDE_* constants
     */
    $Media->getLongitudeRef();
    /**
     * Returns Latitude reference (North or South) and equals to one of the
     * \MediaVorus\Media\DefaultMedia::GPSREF_LATITUDE_* constants
     */
    $Media->getLatitudeRef();
}

```


\MediaVorus\Media\Image
-----------------------

Media Image extends the default media, so it acquires all its method.
It has much more methods and provides the following informations :


```php
<?php

$Media = \MediaVorus\Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

if($Media instanceof \MediaVorus\Media\Image)
{
    /**
     * It extends the DefaultMedia
     */
    assert($Media instanceof \MediaVorus\Media\DefaultMedia);
    /**
     * Returns the width (int)
     */
    $Media->getWidth();
    /**
     * Returns the height (int)
     */
    $Media->getHeight();
    /**
     * Returns the number of channels (int)
     */
    $Media->getChannels();
    /**
     * Returns the focal length (string), not parsed
     */
    $Media->getFocalLength();
    /**
     * Returns the color depth in bits (int)
     */
    $Media->getColorDepth();
    /**
     * Returns the camera model name (string)
     */
    $Media->getCameraModel();
    /**
     * Returns true if the flash has been fired (bool)
     */
    $Media->getFlashFired();
    /**
     * Returns the aperture (string), not parsed
     */
    $Media->getAperture();
    /**
     * Returns the shutter speed (string), not parsed
     */
    $Media->getShutterSpeed();
    /**
     * Returns the orientation (string), one of the \MediaVorus\Media\Image::ORIENTATION_*
     */
    $Media->getOrientation();
    /**
     * Returns the date when the photos has been taken (string), not parsed
     */
    $Media->getCreationDate();
    /**
     * Returns the hyperfocal distance (string), not parsed
     */
    $Media->getHyperfocalDistance();
    /**
     * Returns the ISO value (int)
     */
    $Media->getISO();
    /**
     * Returns the light value (string), not parsed
     */
    $Media->getLightValue();
}

```



