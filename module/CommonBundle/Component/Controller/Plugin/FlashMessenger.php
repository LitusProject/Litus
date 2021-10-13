<?php

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Component\FlashMessenger\FlashMessage;

/**
 * Make FlashMessenger accept FlashMessages as well.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FlashMessenger extends \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger
{
    /**
     * @param  string $type
     * @param  string $title
     * @param  string $message
     * @return self
     */
    private function addMessageHelper($type, $title, $message)
    {
        return $this->addMessage(
            new FlashMessage($type, $title, $message)
        );
    }

    /**
     * Add a message.
     *
     * Accepts strings to provide compatibility with frameworks that use this.
     *
     * @param  FlashMessage|string $message
     * @param  string|null         $namespace
     * @param  integer|null        $hops
     * @return self                Provides a fluent interface
     */
    public function addMessage($message, $namespace = null, $hops = 1)
    {
        if (is_string($message)) {
            switch ($this->getNamespace()) {
                case self::NAMESPACE_ERROR:
                    $type = FlashMessage::ERROR;
                    $title = 'Error';
                    break;
                case self::NAMESPACE_WARNING:
                    $type = FlashMessage::WARNING;
                    $title = 'Warning';
                    break;
                case self::NAMESPACE_SUCCESS:
                    $type = FlashMessage::SUCCESS;
                    $title = 'Success';
                    break;
                default:
                    $type = FlashMessage::NOTICE;
                    $title = 'Notice';
            }

            return $this->addMessageHelper($type, $title, $message);
        }

        return parent::addMessage($message);
    }

    /**
     * @param  string $title
     * @param  string $message
     * @return self
     */
    public function error($title, $message)
    {
        return $this->addMessageHelper(FlashMessage::ERROR, $title, $message);
    }

    /**
     * @param  string $title
     * @param  string $message
     * @return self
     */
    public function warn($title, $message)
    {
        return $this->addMessageHelper(FlashMessage::WARNING, $title, $message);
    }

    /**
     * @param  string $title
     * @param  string $message
     * @return self
     */
    public function success($title, $message)
    {
        return $this->addMessageHelper(FlashMessage::SUCCESS, $title, $message);
    }

    /**
     * @param  string $title
     * @param  string $message
     * @return self
     */
    public function notice($title, $message)
    {
        return $this->addMessageHelper(FlashMessage::NOTICE, $title, $message);
    }
}
