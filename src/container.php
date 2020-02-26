<?php

use Pimple\Container;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Makineza\Event\Dispatcher as EventDispatcher;
use Makineza\Framework;

$container = new Container();

$container['context'] = function (Container $container) {
    return new RequestContext();
};

$container['routes'] = $routes;

$container['compiled_routes'] = function (Container $container) {
    $compiledUlrMatcherDumper = new CompiledUrlMatcherDumper($container['routes']);
    
    return $compiledUlrMatcherDumper->getCompiledRoutes();
};

$container['matcher'] = function (Container $container) {
    return new CompiledUrlMatcher($container['compiled_routes'], $container['context']);
};

$container['request_stack'] = function (Container $container) {
    return new RequestStack();
};

$container['controller_resolver'] = function (Container $container) {
    return new ControllerResolver();
};

$container['argument_resolver'] = function (Container $container) {
    return new ArgumentResolver();
};

$container['listener.router'] = function (Container $container) {
    return new RouterListener($container['matcher'], $container['request_stack']);
};

$container['dispatcher'] = function (Container $container) {
    $dispatcher = new EventDispatcher();
    $dispatcher->addSubscriber($container['listener.router']);
    
    return $dispatcher;
};

$container['framework'] = function (Container $container) {
    return new Framework($container['dispatcher'], $container['controller_resolver'], $container['request_stack'],
        $container['argument_resolver']);
};

return $container;