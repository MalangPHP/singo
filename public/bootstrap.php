<?php

$app->init();

$app->registerCommands(
    [
        \Singo\App\Commands\LoginCommand::class
    ],
    function () use ($app) {
        return new \Singo\App\Handlers\UserHandler($app["security.jwt.encoder"]);
    }
);

$app["login.controller"] = function(\Pimple\Container $container) {
    return new \Singo\App\Controllers\AuthController(
        $container["request_stack"],
        $container["fractal"],
        $container["bus"]
    );
};

/**
 * Login route, no authentication needed
 */
$app->post("/login", "login.controller:loginAction");

/**
 * secured route, authentication needed
 */
$app->post("/checkAuth", "login.controller:isAuthenticatedAction");
