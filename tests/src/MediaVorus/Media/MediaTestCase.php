<?php

namespace MediaVorus\Media;

use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;

class MediaTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getSerializer()
    {
        AnnotationRegistry::registerAutoloadNamespace(
            'JMS\Serializer\Annotation', __DIR__ . '/../../../../vendor/jms/serializer/src'
        );

        return SerializerBuilder::create()
            ->setCacheDir(__DIR__ . '/../../../cache')
            ->setDebug(true)
            ->build();
    }
}

