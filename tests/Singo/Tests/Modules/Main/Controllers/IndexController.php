<?php


namespace Singo\Tests\Modules\Main\Controllers;

use Singo\Contracts\Controller\ControllerAbstract;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Symfony\Component\HttpFoundation\Response;

/**
 * @SLX\Controller(prefix="/")
 */
class IndexController extends ControllerAbstract
{
    /**
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="home")
     * )
     */
    public function indexAction()
    {
        return new Response("hello world");
    }
}

