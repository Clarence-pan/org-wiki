<?php

/**
 * Class BaseModel
 */
class BaseModel extends CComponent {
    /**
     * @param array|null $attributes
     */
    public function __construct($attributes=null){
        $this->setAttributes($attributes);
    }

    /**
     * @param $attributes array
     */
    public function setAttributes($attributes){
        if (!$attributes){
            return;
        }

        foreach ($attributes as $attr => $value) {
            $this->{$attr} = $value;
        }
    }
}