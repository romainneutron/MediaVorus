<?php

namespace MediaVorus;

use FFMpeg\FFMpegServiceProvider;
use PHPExiftool\PHPExiftoolServiceProvider;
use Silex\Application;

class MediaVorusServiceProvideTest extends \PHPUnit_Framework_TestCase
{
    private function getApplication()
    {
        return new Application();
    }

    /**
     *
     */
    public function testInitialization()
    {
        $app = $this->getApplication();

        $app->register(new MediaVorusServiceProvider());
        $app->register(new PHPExiftoolServiceProvider());
        $app->register(new FFMpegServiceProvider());

        $this->assertInstanceOf('\\MediaVorus\\MediaVorus', $app['mediavorus']);
    }

    /**
     * @expectedException MediaVorus\Exception\RuntimeException
     */
    public function testFailOnFFmpeg()
    {
        $app = $this->getApplication();

        $app->register(new MediaVorusServiceProvider());
        $app->register(new PHPExiftoolServiceProvider());

        $app->boot();
    }

    /**
     * @expectedException MediaVorus\Exception\RuntimeException
     */
    public function testFailOnExiftool()
    {
        $app = $this->getApplication();

        $app->register(new MediaVorusServiceProvider());
        $app->register(new FFMpegServiceProvider());

        $app->boot();
    }
}