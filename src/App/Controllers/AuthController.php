<?php


namespace Singo\App\Controllers;

use Singo\App\Commands\LoginCommand;
use Singo\App\Handlers\Exceptions\InvalidCredentialsException;
use Singo\Contracts\Controller\ControllerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 * @package Singo\App\Controllers
 */
class AuthController extends ControllerAbstract
{
    /**
     * @return JsonResponse
     */
    public function loginAction()
    {
        $request = $this->request->getCurrentRequest();

        $command = new LoginCommand();
        $command->setUsername($request->get("username"));
        $command->setPassword($request->get("password"));

        $response = new JsonResponse();

        try {
            $token = $this->bus->handle($command);

            return $response->create(["token" => $token]);
        } catch (\DomainException $e) {
            return $response->create(["error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (InvalidCredentialsException $e) {
            return $response->create(["error" => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return JsonResponse
     */
    public function isAuthenticatedAction()
    {
        return new JsonResponse(["message" => "You are logged user"]);
    }
}

// EOF
