<?php
// This is global bootstrap for autoloading

require "vendor/autoload.php";

$app = new \Singo\Application();

require "public/bootstrap.php";
