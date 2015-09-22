<?php

/**
 * Class String
 */
class String {
    /**
     * @param $str
     */
    public function __construct($str){
        $this->str = $str;
    }

    /**
     * @param $tail
     * @return bool
     */
    public function endWith($tail){
        $tailLen = strlen($tail);
        return substr($this->str, $this->getLength() - $tailLen, $tailLen) === $tail;
    }

    /**
     * @param $tail
     * @return string
     */
    public function cutTail($tail){
        return substr($this->str, 0, $this->getLength() - strlen($tail));
    }

    public function __toString(){
        return $this->str;
    }

    /**
     * @return int
     */
    public function getLength(){
        return strlen($this->str);
    }

    public static function from($str){
        return new String($str);
    }

    protected $str;
}