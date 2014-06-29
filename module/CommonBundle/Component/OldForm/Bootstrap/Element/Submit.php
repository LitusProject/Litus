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

namespace CommonBundle\Component\OldForm\Bootstrap\Element;

/**
 * Submit form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Submit extends \Zend\Form\Element\Submit
{
    /**
     * @param  null|int|string                    $name    Optional name for the element
     * @param  array                              $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name, $options = null)
    {
        parent::__construct($name, $options);
        $this->setAttribute('id', $name);
        $this->setAttribute('class', 'btn btn-primary');
    }
}
