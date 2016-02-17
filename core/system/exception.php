<?php

class CustomException extends Exception {

    protected $title	= 'Framework error';  	// default exception title
    protected $message	= 'Unknown exception';  // default exception message
    protected $code		= 0;                    // user defined exception code
    protected $file		= '';                 	// source filename of exception
    protected $line		= 0;                    // source line of exception

    public function __construct($message = null, $code = 0)
    {
    	$this->code = $code;
//    	$this->trace = array_slice(Debug::getTrace(), 1);
    	$this->setMessage($message);
    }

    protected function getTitle()
    {
    	return $this->title;
    }

	protected function setMessage($message)
    {
    	if (!$message) {
    		$message = $this->message;
    	}
    	$this->message = $this->getTitle() . ': ' . $message;

    	echo $this->message;
    	exit();
    }

}