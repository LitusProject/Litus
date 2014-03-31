<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Controller\Plugin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    ArrayIterator,
    Zend\Stdlib\SplQueue,
    Zend\Session\Container,
    Zend\Session\ManagerInterface as Manager;

/**
 * Make FlashMessenger accept FlashMessages as well.
 *
 * @author Bram Gotink <bram.gotink@litus.cc>
 */
class FlashMessenger extends \Zend\Mvc\Controller\Plugin\FlashMessenger
{
    /**
     * Add a message.
     *
     * Accepts strings to provide compatibility with frameworks that use this.
     *
     * @param  string|\CommonBundle\Component\FlashMessenger\FlashMessage $message
     * @return \CommonBundle\Component\Controller\Plugin\FlashMessenger   Provides a fluent interface
     */
    public function addMessage($message)
    {
        if (is_string($message)) {
            $type = FlashMessage::NOTICE;
            $title = 'Notice';

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

            $message = new FlashMessage($type, $title, $message);
        }

        return parent::addMessage($message);
    }
}
