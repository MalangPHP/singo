<?php

$loader = require __DIR__ . "/../vendor/autoload.php";

$loader->add("Singo\\Tests", __DIR__);

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
