<?php

require "vendor/autoload.php";

$app = new \Singo\Application();

$app->init();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($app["orm.em"]);
