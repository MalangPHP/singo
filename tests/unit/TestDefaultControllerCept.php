<?php

$I = new UnitTester($scenario);
$I->wantTo('test default controller');

$container = Singo\Application::$container;

$controller = $container["test.controller"];

$response = $controller->indexAction();

$I->assertTrue($response instanceof \Symfony\Component\HttpFoundation\JsonResponse);
