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

namespace CommonBundle\Component\Lilo\Data;

/**
 * This class converts an exception to the right format for the
 * Lilo API.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Log extends \CommonBundle\Component\Lilo\Data
{
    /**
     * @var array The correctly formatted data object
     */
    private $_data = array();

    /**
     * Construct a new Exception object.
     *
     * @param \Exception $exception The exception that should be formatted
     */
    public function __construct($message, $tags)
    {
        $this->_data = array(
            'message' => $message,
            'tags' => $tags
        );
    }

    /**
     * Encodes the data in a JSON object.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->_data);
    }
}
