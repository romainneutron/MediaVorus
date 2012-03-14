#MediaVorus

A tiny lib for playing with MultiMedia Files


MediaVorus is a small PHP library wich provide a set of tools to deal with
multimedia files

ex :

<?php

$Media = \MediaVorus\Media::guess('myRawFile.cr2');

assert($Media instanceof '\MediaVorus\Media\Image');
assert($Media->getMediaType() === \MediaVorus\Media::ImageType);
assert($Media->isRaw());

