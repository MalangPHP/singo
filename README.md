## Singo

Singo is a skeleton apps based on [Silex](http://silex.sensiolabs.org/) micro framework to facilitate developers to create HTTP REST based API.

## Technology Stack

* [Silex](http://silex.sensiolabs.org/) - Micro Framework
* [Doctrine ORM/DBAL](http://www.doctrine-project.org/index.html) - ORM and Database Abstraction Layer
* [Swiftmailer](http://swiftmailer.org/) - Email Interface
* [Fractal](http://fractal.thephpleague.com/) - Manipulate Complex API response

## Application Architecture

![Architecture](http://i.imgur.com/WP8qXpl.png)

Singo's architecture use [Command](http://sourcemaking.com/design_patterns/command) design pattern to create re-usable code (can be used for another interface e.g CLI, Web, API, etc). By using `Handler Middleware` you can manipulate `Command` object before processed by `Handler` e.g. you can do `Command` validation with `Handler Middleware`.

## How to use

### Create controller
Create a controller inside `src/App/Controllers` folder with a class extend `Singo\Contracts\Controller\ControllerAbstract`. Here is an example of qualify controller:

~~~php
<?php


namespace Singo\App\Controllers;

use League\Fractal\Resource\Item;
use Singo\App\Response\Transformer\TestTransformer;
use Singo\App\Commands\TestCommand;
use Singo\Contracts\Controller\ControllerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class TestController
 * @package Singo\Controllers
 */
class TestController extends ControllerAbstract
{
    /**
     * @return JsonResponse
     */
    public function indexAction()
    {
        $command = new TestCommand();
        $command->name = "P";
        $command->email = "pras@openmailbox.org";
        $command->location = "Malang";

        $response = $this->bus->handle($command);
        $resource = new Item($response, new TestTransformer());

        return new JsonResponse($this->fractal->createData($resource)->toArray());
    }
}

// EOF
~~~

It is allowed to create `method` within a `controller` class.

### Create command
Create `Command` class inside `src/App/Commands` folder which require you to implement `Singo\Contracts\Bus\CommandInterface` interface. Within `Command` class, there is only messages which will be processed by `Handler`. Here is an example of qualify `Command` class:

~~~php
<?php


namespace Singo\App\Commands;

use Singo\Contracts\Bus\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TestCommand
 * @package Sable\Commands
 */
class TestCommand implements CommandInterface
{
    /**
     * @Assert\Length(min = 3)
     * @Assert\NotBlank
     * @var string
     */
    public $name;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @var string
     */
    public $email;

    /**
     * @Assert\Length(min = 2)
     * @Assert\NotBlank
     * @var string
     */
    public $location;
}

// EOF
~~~

You can also add property validation by add annotation on property. You can check it [here](http://symfony.com/doc/current/book/validation.html#constraints) for reference.

### Create command handler
`Command Handler` is used to process `Command` that you pass via `Controller`. To create `Command Handler`, you must implement `Singo\Contracts\Bus\HandlerInterface` and must have `handle<Command_name>` method. So, if we have a `Command` class called `UserRegistrationCommand`, then we need to create method for `Command Handler` with `handleUserRegistrationCommand`. Here is a qualify example:

~~~php
<?php


namespace Singo\App\Handlers;

use Singo\App\Commands\TestCommand;
use Singo\Contracts\Bus\HandlerInterface;

/**
 * Class TestHandler
 * @package Singo\Handlers
 */
class TestHandler implements HandlerInterface
{
    /**
     * @param TestCommand $command
     * @return array
     */
    public function handleTestCommand(TestCommand $command)
    {
        return [
            "name"  => $command->name,
            "email" => $command->email,
            "location" => $command->location
        ];
    }
}

// EOF
~~~

### Register controller and command
Last step is register your `Controller` and `Command` to our bootstrap on `public/bootstrap.php` folder. Here is an example to register your `Controller` and `Command`:

~~~php
<?php

// Initialize
$app->init();

/**
* Register command and handler.
* One class handler can handle multiple command
*/
$app->registerCommands(
    [\Singo\App\Commands\TestCommand::class],
    function () use ($app) {
        return new \Singo\App\Handlers\TestHandler();
    }
);

/**
* Register controller kedalam conatiner
*/
$app["test.controller"] = function(\Pimple\Container $container) {
    return new \Singo\App\Controllers\TestController(
        $container["request_stack"],
        $container["fractal"],
        $container["bus"]
    );
};

/**
* For routing
* /
$app->get("/", "test.controller:indexAction");
~~~

### Register Event
First, you need to create `SubscriberClass` class which implement `Symfony\Component\EventDispatcher\EventSubscriberInterface`. Here is an example of `SubscriberClass`:

~~~php
<?php

use Pimple\Container;
use Singo\Bus\Exception\InvalidCommandException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ExceptionHandler
 * @package Singo\Event\Listener
 */
final class ExceptionHandler implements EventSubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onSilexError(GetResponseForExceptionEvent $event)
    {
        if ($this->container["sable.config"]->get("common/debug")) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof InvalidCommandException) {
            $message = explode("|", $exception->getMessage());

            $event->setResponse(new JsonResponse(
                [
                    "error" =>
                    [
                        $message[0] => $message[1]
                    ]
                ]
            ), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        // tulis event yang di listen dalam array
        return [
            KernelEvents::EXCEPTION => "onSilexError"
        ];
    }
}

// EOF
~~~

Then, register `SubscriberClass` to our application while bootstrapping.

~~~php
$app->registerSubscriber(
    ExceptionHandler:class,
    function () {
        return new ExceptionHandler();
    }
);
~~~

For further documentation about event dispatcher , please read it [here](http://symfony.com/doc/current/components/event_dispatcher/introduction.html).