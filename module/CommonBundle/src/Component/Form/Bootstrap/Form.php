<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Form\Bootstrap;

use CommonBundle\Component\Form\Bootstrap\DisplayGroup\Actions;

/**
 * Extending Zend's form component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Form extends \Zend\Form\Form
{
    /**
     * @param mixed $options The form's options
     */
    public function __construct($options)
    {
        parent::__construct($options = null);

        $this->setMethod('post');

        $this->setAttrib('class', 'form-horizontal');
        $this->removeDecorator('HtmlTag');
    }

    public function setActionsGroup($elements)
    {
        $group = array();
        foreach ($elements as $element) {
            if (isset($this->_elements[$element])) {
                $add = $this->getElement($element);
                if (null !== $add) {
                    unset($this->_order[$element]);
                    $group[] = $add;
                }
            }
        }

        $actions = new Actions('form_actions', $this->getPluginLoader(self::DECORATOR), array('elements' => $group));
        $this->_addDisplayGroupObject($actions);
    }
}
