## Singo

Singo adalah sebuah applikasi skeleton berbasis micro framework [Silex](http://silex.sensiolabs.org/) yang bertujuan untuk mempermudah developer untuk membuat HTTP REST based API.

## Arsitektur Aplikasi

![Arsitektur](http://i.imgur.com/WP8qXpl.png)

Arsitektur Singo menggunakan design pattern [Command](http://sourcemaking.com/design_patterns/command) yang bertujuan agar code yang anda buat bisa digunakan oleh interface lain (CLI, Web, API, dll). Dengan memanfaatkan `Handler Middleware` anda bisa memanipulasi `Command` object sebelum diproses oleh `Handler`. Contoh yang bisa anda lakukan dengan `Handler Middlerware` adalah validasi `Command`, logging event, dll.

## Cara Penggunaan

### Buat Controller
Buat controller didalam folder `src/App/Controllers` dengan kelas yang extend `Singo\Contracts\Controller\ControllerAbstract`. Berikut contoh controller yang memenuhi syarat.

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

Dalam suatu kelas `controller` diperbolehkan untuk membuat beberapa `method`.

### Buat Command
Buat kelas `Command` didalam folder `src/App/Commands` dengan syarat kelas yang anda buat harus mengimplementasi interface `Singo\Contracts\Bus\CommandInterface`. Di dalam kelas `Command` hanya berisi sebuah pesan yang nanti akan diproses oleh `Handler`. Berikut contoh kelas `Command` yang memenuhi syarat.

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

Anda juga dapat menambahkan validasi property dengan cara menambahkan annotation pada property. Untuk refrensi validasi dengan menggunakan  annotation dapat dilihat [disini](http://symfony.com/doc/current/book/validation.html#constraints).

### Buat Command Handler
`Command Handler` berfungsi untuk mengolah `Command` yang anda kirimkan melalui `Controller`. Syarat untuk membuat `Command Handler` adalah mengimplementasi `Singo\Contracts\Bus\HandlerInterface` dan mempunyai name method `handlerNamaCommand`. Jadi semisal nama kelas `Command` kita adalah `UserRegistrationCommand`maka nama method untuk `Command Handler` kita harus `handleUserRegistrationCommand`. Berikut contoh `Command Handler` yang memenuhi syarat.

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

### Register Controller dan Command
Langkah terakhir adalah meregistrasikan `Controller` dan `Command` anda didalam file `bootstrap.php` yang ada difolder `public/bootstrap.php`. Berikut contoh code untuk mendaftarkan `Controller` dan `Command`

~~~php
<?php

// Lakukan initialisasi
$app->init();

/**
* Register command dan handler
* 1 kelas handler bisa menangani beberapa command
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
* Buat routing nya
* /
$app->get("/", "test.controller:indexAction");
~~~

### Register Event

Pertama anda harus membuat `SubscriberClass` yang mengimplementasi `Symfony\Component\EventDispatcher\EventSubscriberInterface`. Berikut contoh `SubscriberClass`

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

Kemudian daftarkan `SubscriberClass` kedalam applikasi pada saat boorstraping.

~~~php
$app->registerSubscriber(
    ExceptionHandler:class,
    function () {
        return new ExceptionHandler();
    }
);
~~~

Untuk dokumentasi lebih lanjut silahkan baca tautan [berikut](http://symfony.com/doc/current/components/event_dispatcher/introduction.html).
