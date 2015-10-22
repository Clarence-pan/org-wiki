<?php


namespace html\err;


class InvalidPropertyException extends \Exception{
    public function __construct($propertyName){
        parent::__construct("Invalid property: {$propertyName}");
    }
} 