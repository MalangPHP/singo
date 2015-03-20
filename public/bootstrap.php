<?php

$app->init();

$app->registerCommands(
    [\Singo\App\Commands\TestCommand::class],
    function () use ($app) {
        return new \Singo\App\Handlers\TestHandler();
    }
);

$app["test.controller"] = function(\Pimple\Container $container) {
    return new \Singo\App\Controllers\TestController(
        $container["request_stack"],
        $container["fractal"],
        $container["bus"]
    );
};

$app->get("/", "test.controller:indexAction");
