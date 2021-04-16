<?php

class AtualizacaoBaseCorreiosException extends Exception {
    
    protected $array_message;
    
    public function __construct($message, $code = null, $previous = null) {
        parent::__construct($message["log"], $code, $previous);
        //echo $message['log'];
        $this->array_message = $message;
    }
    
    public function getArrayMessage() {
        return $this->array_message;
    }
    
}

