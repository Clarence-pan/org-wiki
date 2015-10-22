<?php


namespace html\parser\org;


class TextReader {
    public function __construct($text){
        $this->lines = explode("\n", $text);
        array_unshift($this->lines, '');
    }

    public function rewind(){
        reset($this->lines);
        return $this;
    }

    public function prev(){
        return prev($this->lines);
    }

    public function next(){
        return next($this->lines);
    }

    public function eof(){
        $next = next($this->lines);
        prev($this->lines);
        return $next === false;
    }

    private $lines;
} 