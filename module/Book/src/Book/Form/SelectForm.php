<?php
namespace Book\Form;

use Zend\Form\Form;

class SelectForm extends Form{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('select');


    }
}