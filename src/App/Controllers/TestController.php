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
