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

use \Zend\Form\Decorator;

/**
 * Extending Zend's form element component, so that our forms look the way we want
 * them to.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Element extends \Zend\Form\Element
{
    /**
     * Load default decorators
     *
     * @todo Change errors decorator to be awesomeness.
     * @return CommonBundle\Component\Form\Bootstrap\Element
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $getId = function(Decorator $decorator) {
                return $decorator->getElement()->getId() . '-element';
            };
            $this->addDecorator('ViewHelper')
                 ->addDecorator('Errors', array('class' => 'help-block'))
                 ->addDecorator('Description', array('tag' => 'span', 'class' => 'help-block'))
                 ->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls'))
                 ->addDecorator('Label', array('class' => 'control-label'))
                 ->addDecorator('HtmlTag', array('class' => 'control-group', 'tag' => 'div'));
        }
        return $this;
    }
    
    /**
     * Specifies whether this element is a required field.
     *
     * Also sets the HTML5 'required' attribute.
     *
     * @param boolean $flag
     * @return void
     */
    public function setRequired($flag = true)
    {
        if ($flag) {
            $this->required = 'true';
        } else {
            if (isset($this->required)) {
                unset($this->required);
            }
        }
        return parent::setRequired($flag);
    }
}
