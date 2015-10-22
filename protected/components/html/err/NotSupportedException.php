<?php


namespace html\err;


class NotSupportedException extends \Exception{
    public function __construct($what){
        parent::__construct("$what is not supported yet");
    }
} 