<?php
/**
 * address_book.
 * Date: 13/01/15
 */

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/AddressBook/Controller/BaseController.php');
require_once(__DIR__ . '/../src/AddressBook/Controller/MainController.php');
require_once('TwigEngine.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

try {
    $parameters = Yaml::parse(file_get_contents(__DIR__ . '/config/parameters.yml'));
    $routesCollection = Yaml::parse(file_get_contents(__DIR__ . '/config/routes.yml'));

    $routes =  new RouteCollection();
    foreach ($routesCollection as $name => $route) {
        $routes->add($name, new Route($route['path'], ['controller' => $route['controller'], 'action' => $route['action'], 'extra' => $route]));
    }

    $context = new RequestContext($_SERVER['REQUEST_URI']);

    $matcher = new UrlMatcher($routes, $context);

    $request = Request::createFromGlobals();

    $session = new Session();
    $session->start();

    $request->setSession($session);

    $templatesDir = __DIR__ . '/Resources/views/';
    $templateNameParser = new TemplateNameParser();
    $templating = new TwigEngine(new Twig_Environment(), $templateNameParser, $templatesDir);

    try {
        $match = $matcher->matchRequest($request);

        $controller = new $match['controller']($request, $templating, $parameters['parameters'], $match);

        echo call_user_func([$controller, 'renderAction'], $match['action']);

    } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e)
    {
        echo sprintf('<html><body><h1>%s</h1></body></html>', $e->getMessage());
    }

} catch (ParseException $e) {
    printf("Unable to parse the YAML string: %s", $e->getMessage());
}





