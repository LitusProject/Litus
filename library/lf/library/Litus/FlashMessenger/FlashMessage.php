<?php

namespace Litus\FlashMessenger;

class FlashMessage extends \Zend\Controller\Plugin\AbstractPlugin
{
	const ERROR = 'error_message';
	const WARNING = 'warning_message';
	const SUCCESS = 'success_message';
	const NOTICE = 'notice_message';
	
	private $type;
	private $title;
	private $message;
	
	public function __construct($type, $title, $message)
	{
		$this->type = $type;
		$this->title = $title;
		$this->message = $message;
	}
	
	public function __tostring()
	{
		return "<div class=\"$this->type full_width\">"
			."<div class=\"title\">$this->title</div>"
			."<div class=\"content\"><p>$this->message</p></div>"
			."</div>";
	}
}
