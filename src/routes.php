<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Calendar\Controller\LeapYearController;

$routes = new RouteCollection();

$routes->add('leap_year', new Route('/is_leap_year/{year}', ['year' => null, '_controller' => [LeapYearController::class, 'index']]));

return $routes;