<?php

/**
 * Class Lazy
 */
class Lazy
{
    private $initialize;
    private $result = null;

    /**
     * @param callable $initialize the initialize function
     */
    public function __construct($initialize){
        $this->initialize = $initialize;
    }

    /**
     * @return mixed the result
     */
    public function get(){
        return self::init($this->result, $this->initialize);
    }

    /**
     * init var if null
     * @param mixed $var
     * @param callable $initialize
     * @return mixed
     */
    public static function init(&$var, $initialize){
        if ($var === null){
            $var = call_user_func($initialize);
        }

        return $var;
    }

} 