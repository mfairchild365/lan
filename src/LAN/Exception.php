<?php
namespace LAN;

class Exception extends \Exception implements Renderable
{
    public function __construct($message = "", $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        $array = array();

        $array['message'] = $this->message;
        $array['code']    = $this->code;

        return $array;
    }
}