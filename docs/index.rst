Presentation
============


This lib is under heavy development !

MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

Example
-------

.. code-block:: php

    <?php

    $Media = \MediaVorus\Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

    if($Media instanceof \MediaVorus\Media\Image)
    {
        echo sprintf("Found a file with dimensions : %dx%d", $Media->getWidth(), $Media->getHeight());
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


API
===

Guesser
-------

.. code-block:: php

    <?php

    $Media = \MediaVorus\Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

    //Returns a \Doctrine\Common\Collections\ArrayCollection of Medias
    $MediaCollection = \MediaVorus\Media::inspectDirectory($dir, $recursive);


Medias
------

Media\\DefaultMedia
*******************

Default Media is the Default container.
This object provides GPS informations :


.. code-block:: php

    <?php

    use MediaVorus\Media;

    $Media = Media::guess(new \SplFileInfo('somefile.smf'));

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

    use MediaVorus\Media;

    $Media = Media::guess(new \SplFileInfo('tests/files/ExifTool.jpg'));

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




Contents:

.. toctree::
   :maxdepth: 2

