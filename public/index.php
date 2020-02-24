<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Makineza\Listener\ContentLengthListener;
use Makineza\Listener\GoogleListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;

function render_template(Request $request)
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__ . '/../src/pages/%s.php', $_route);
    
    return new Response(ob_get_clean());
}

$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/routes.php';

$context = new Routing\RequestContext();
$compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
$matcher = new CompiledUrlMatcher($compiledRoutes, $context);

$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new ContentLengthListener());
$dispatcher->addSubscriber(new GoogleListener());

$framework = new Makineza\Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
$framework = new HttpKernel\HttpCache\HttpCache(
    $framework,
    new HttpKernel\HttpCache\Store(__DIR__.'/../cache'),
    new HttpKernel\HttpCache\Esi(),
    ['debug' => false]
);

$framework->handle($request)->send();