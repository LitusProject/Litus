<?php

namespace Litus\FlashMessenger;

class FlashMessage extends \Zend\Controller\Plugin\AbstractPlugin
{

	const ERROR = 'error_message';
	const WARNING = 'warning_message';
	const SUCCESS = 'success_message';
	const NOTICE = 'notice_message';

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
     * @var bool Whether or not the message should be displayed full width
     */
    private $_fullWidth = false;

    /**
     * Create new FlashMessage.
     *
     * @param string $type The FlashMessage's type
     * @param string $title The FlashMessage's title
     * @param string $message The FlashMessage's message
     * @param bool $fullWidth Whether or not the message should be displayed full width
     */
	public function __construct($type, $title, $message, $fullWidth = false)
	{
		$this->_type = $type;
		$this->_title = $title;
		$this->_message = $message;
        $this->_fullWidth = $fullWidth;
	}

    /**
     * Return a string version of this message that will be used following an echo call in
     * a view file.
     *
     * @return string
     */
	public function __toString()
	{
		return '<div class="' . $this->_type . (true === $this->_fullWidth ? ' full_width' : '') . '">'
			.'<div class="title">' . $this->_title . '</div>'
			.'<div class="content"><p>' . $this->_message . '</p></div>'
			.'</div>';
	}
}
