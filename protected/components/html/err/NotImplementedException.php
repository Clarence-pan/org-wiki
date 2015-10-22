<?php


namespace html\err;


class NotImplementedException extends \Exception{
    public function __construct($what){
        parent::__construct("$what is not implemented yet");
    }
} 