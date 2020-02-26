<?php

namespace Providers;

use Makineza\Event\SubscriptionProvider;
use Makineza\Listener\StringResponseListener;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;

class EventSubscriptionProvider extends SubscriptionProvider
{
    
    protected function load()
    {
        $this->subscribers = [
            new StringResponseListener(),
            new ResponseListener('UTF-8'),
            new ErrorListener('Makineza\Exception\ExceptionHandler::handle'),
        ];
    }
}

