<?php

namespace Calendar\Controller;

use Calendar\Model\LeapYear;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LeapYearController
{
    
    public function index(Request $request, $year)
    {
        $leapYear = new LeapYear();
        if ($leapYear->isLeapYear($year)) {
            return new Response(json_encode(['test' => 'Yep, this is a leap year!', 'name' => 'Albert']));
        }
        
        return 'Nope, this is not a leap year.';
    }
}