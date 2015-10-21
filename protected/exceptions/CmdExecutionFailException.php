<?php

/**
 * Class CmdExecutionFailException
 */
class CmdExecutionFailException extends Exception {
    /**
     * @param string $cmd
     * @param int    $code
     * @param null   $innerException
     */
    public function __construct($cmd, $code, $innerException=null){
        parent::__construct(sprintf("Command '%s' execution return %d.", $cmd, $code), $code, $innerException);
    }

} 