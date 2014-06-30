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

namespace CommonBundle\Component\Form\Bootstrap;

use Zend\Form\Element\Csrf;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
abstract class Form extends \CommonBundle\Component\Form\Form
{
    /**
     * @var boolean Whether or not to show the form-actions div
     */
    private $_displayFormActions;

    /**
     * @param null|string|int $name               Optional name for the element
     * @param boolean         $horizontal         Whether to display the form horizontically or vertically
     * @param boolean         $displayFormActions Whether or not to show the form-actions div
     */
    public function __construct($name = null, $horizontal = true, $displayFormActions = true)
    {
        parent::__construct($name);

        $this->_displayFormActions = $displayFormActions;

        if ($horizontal)
            $this->setAttribute('class', 'form-horizontal');

        $this->add(
            new Csrf('csrf')
        );
    }

    /**
     * Whether or not to show the form-actions div
     *
     * @return boolean
     */
    public function getDisplayFormActions()
    {
        return $this->_displayFormActions;
    }
}
