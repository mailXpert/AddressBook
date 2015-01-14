<?php
/**
 * address_book.
 * Date: 13/01/15
 */

namespace AddressBook\Controller;


use Symfony\Component\HttpFoundation\Request;
use TwigEngine;

abstract class BaseController
{

    /**
     * @var TwigEngine
     */
    private $engine;
    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(Request $request, TwigEngine $engine, array $parameters)
    {

        $this->engine = $engine;
        $this->request = $request;
        $this->parameters = $parameters;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter($key, $default = null)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        } else {
            return $default;
        }
    }

    public function renderAction($action)
    {
        if ($action != 'login' && !$this->getRequest()->getSession()->get('access_token')) {
            $this->redirect('/login');
        }

        return call_user_func([$this, $action]);
    }

    public function render($name, array $parameters = [])
    {
        return $this->engine->render($name, $parameters);
    }

    public function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

}