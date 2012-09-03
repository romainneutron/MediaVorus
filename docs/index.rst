Presentation
============


This lib is under heavy development !

MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

Example
-------

.. code-block:: php

    <?php

    use MediaVorus\MediaVorus;
    use MediaVorus\MediaVorus;
    use MediaVorus\Media\Image;

    $mediavorus = MediaVorus::create();
    $Media = $mediavorus->guess('tests/files/CanonRaw.CR2');

    if($Media instanceof Image)
    {
        echo sprintf("Image was taken with a %f shutter speed", $Media->getShutterSpeed());
    }


Goals
-----

MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

The aim is to provide an abstract layer between the program and the multimedia
file.

This library is built with the Symfony\HttpFoundation component
and PHPExiftool.
Doctrine Common Package is also required to take advantage of the powerful
ArrayCollection.

First, the need is to analyze multimedia files and get their properties.

In a very next future, we would add metadata mapper to handle various
configurations.

Silex Service Provider
----------------------

MediaVorus comes bundled with its `Silex Service Provider <silex.sensiolabs.org>`_.
As MediaVorus relies on `PHP-Exiftool <https://github.com/romainneutron/PHPExiftool>`_
and `FFProbe <http://ffmpeg-php.readthedocs.org/>`_, you'll have to register
both bundles to use it :

.. code-block:: php

    <?php

    use FFMpeg\FFMpegServiceProvider;
    use MediaVorus\MediaVorusServiceProvider;
    use PHPExiftool\PHPExiftoolServiceProvider;
    use Silex\Application;

    $app = new Application();

    $app->register(new MediaVorusServiceProvider());
    $app->register(new PHPExiftoolServiceProvider());
    $app->register(new FFMpegServiceProvider());

    // you will now have access to $app['mediavorus']
    $video = $app['mediavorus']->guess('/path/to/video/file');

API
===

Guesser
-------

.. code-block:: php

    <?php

    use MediaVorus\MediaVorus;

    $Media = MediaVorus::guess('tests/files/ExifTool.jpg');

    //Returns a \Doctrine\Common\Collections\ArrayCollection of Medias
    $MediaCollection = MediaVorus::inspectDirectory($dir, $recursive);


Medias
------

Media\\DefaultMedia
*******************

Default Media is the Default container.
This object provides GPS informations :


.. code-block:: php

    <?php

    use MediaVorus\MediaVorus;
    use MediaVorus\Media;

    $Media = MediaVorus::guess('somefile.smf');

    if($Media instanceof Media\DefaultMedia)
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
        * Media\DefaultMedia::GPSREF_LONGITUDE_* constants
        */
        $Media->getLongitudeRef();
        /**
        * Returns Latitude reference (North or South) and equals to one of the
        * Media\DefaultMedia::GPSREF_LATITUDE_* constants
        */
        $Media->getLatitudeRef();
    }


Media\\Image
************

Media Image extends the default media.
It has much more methods and provides the following informations :


.. code-block:: php

    <?php

    use MediaVorus\MediaVorus;
    use MediaVorus\Media;

    $Media = MediaVorus::guess('tests/files/ExifTool.jpg');

    if($Media instanceof Media\Image)
    {
        /**
        * It extends the DefaultMedia
        */
        assert($Media instanceof Media\DefaultMedia);
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
        * Returns the orientation (string), one of the Media\Image::ORIENTATION_*
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




.. toctree::
   :maxdepth: 1

   API
   UseCases
