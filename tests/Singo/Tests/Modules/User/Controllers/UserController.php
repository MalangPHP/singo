<?php


namespace Singo\Tests\Modules\User\Controllers;

use Singo\Contracts\Controller\ControllerAbstract;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/")
 * @SLX\RequireHttps
 */
class UserController extends ControllerAbstract
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="/")
     * )
     */
    public function indexAction()
    {
        return new Response("Hello world");
    }
}

