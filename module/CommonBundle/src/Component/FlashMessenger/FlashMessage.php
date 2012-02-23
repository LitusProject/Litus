<?php

namespace CommonBundle\Component\FlashMessenger;

class FlashMessage
{
	const ERROR = 'error';
	const WARNING = 'warning';
	const SUCCESS = 'success';
	const NOTICE = 'info';

    /**
     * @var string The FlashMessage's type
     */
	private $_type = '';

    /**
     * @var string The FlashMessage's title
     */
	private $_title = '';

    /**
     * @var string The FlasMessage's message
     */
	private $_message = '';

    /**
     * @param string $type The FlashMessage's type
     * @param string $title The FlashMessage's title
     * @param string $message The FlashMessage's message
     * @param bool $fullWidth Whether or not the message should use the full width when displayed
     */
	public function __construct($type, $title, $message)
	{
		$this->_type = $type;
		$this->_title = $title;
		$this->_message = $message;
	}
	
	/**
	 * @return string
	 */
    public function getType() {
        return $this->_type;
    }
    
    /**
     * @return string
     */
	public function getTitle() {
	    return $this->_title;
	}
	
	/**
	 * @return string
	 */
	public function getMessage() {
	    return $this->_message;
	}
}
