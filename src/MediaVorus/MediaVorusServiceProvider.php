<?php

/*
 * This file is part of MediaVorus.
 *
 * (c) 2012 Romain Neutron <imprec@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MediaVorus;

use MediaVorus\Exception\RuntimeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class MediaVorusServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['mediavorus'] = $app->share(function(Application $app) {

            if ( ! isset($app['exiftool.reader']) || ! isset($app['exiftool.writer'])) {
                throw new RuntimeException('MediaVorus Service Provider requires Exiftool Service Provider');
            }

            if ( ! isset($app['ffmpeg.ffprobe'])) {
                throw new RuntimeException('MediaVorus Service Provider requires FFMpeg Service Provider');
            }

            return new MediaVorus($app['exiftool.reader'], $app['exiftool.writer'], $app['ffmpeg.ffprobe']);
        });
    }

    public function boot(Application $app)
    {

    }
}
