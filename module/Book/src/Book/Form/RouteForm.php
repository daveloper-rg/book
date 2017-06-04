<?php
namespace Book\Form;

use Zend\Form\Form;

class RouteForm extends Form{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('route');


    }
}