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
