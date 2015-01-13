<?php
/**
 * address_book.
 * Date: 13/01/15
 */

namespace AddressBook\Controller;


use Symfony\Component\HttpFoundation\Request;
use TwigEngine;

abstract class BaseController {

    /**
     * @var TwigEngine
     */
    private $engine;
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request, TwigEngine $engine)
    {

        $this->engine = $engine;
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function renderAction($action)
    {
        if ($action != 'login' && !$this->getRequest()->getSession()->get('access_token')) {
            header('Location: /login');
            exit;
        }
        return call_user_func([$this, $action]);
    }

    public function render($name, array $parameters = [])
    {
        return $this->engine->render($name, $parameters);
    }

}