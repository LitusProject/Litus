<?php

namespace CommonBundle\Component\FlashMessenger;

class FlashMessage
{
    const ERROR = 'danger';
    const WARNING = 'warning';
    const SUCCESS = 'success';
    const NOTICE = 'info';

    /**
     * @var string The FlashMessage's type
     */
    private $type = '';

    /**
     * @var string The FlashMessage's title
     */
    private $title = '';

    /**
     * @var string The FlasMessage's message
     */
    private $message = '';

    /**
     * @param string $type    The FlashMessage's type
     * @param string $title   The FlashMessage's title
     * @param string $message The FlashMessage's message
     */
    public function __construct($type, $title, $message)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
