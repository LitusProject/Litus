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
 
namespace CommonBundle\Component\Form\Bootstrap\Element;

/**
 * Checkbox form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Checkbox extends \Zend\Form\Element\Checkbox
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
                ->addDecorator('Description', array('tag' => 'span', 'class' => 'help-block'))
                ->addDecorator(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls'))
                ->addDecorator('Label', array('class' => 'control-label'))
                ->addDecorator('HtmlTag', array('class' => 'control-group', 'tag' => 'div'));
        }
        return $this;
    }
}
