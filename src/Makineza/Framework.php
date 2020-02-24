<?php

namespace Makineza;

use Makineza\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Framework
{
    
    protected $dispatcher;
    protected $matcher;
    protected $controllerResolver;
    protected $argumentResolver;
    
    public function __construct(
        EventDispatcherInterface $dispatcher,
        UrlMatcherInterface $matcher,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->dispatcher = $dispatcher;
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }
    
    public function handle(Request $request)
    {
        $this->matcher->getContext()->fromRequest($request);
        
        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));
            
            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);
            
            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $exception) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $exception) {
            $response = new Response('An error occurred', 500);
        }
    
        $this->dispatcher->dispatch(new ResponseEvent($request, $response), 'response');
        
        return $response;
    }
}