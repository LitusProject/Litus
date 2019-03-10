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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\Form\View\Helper\Bootstrap;

use CommonBundle\Component\Form\ElementInterface;
use InvalidArgumentException;
use Zend\Form\ElementInterface as ZendElementInterface;

/**
 * View helper to render a form element.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormElement extends \Zend\Form\View\Helper\FormElement
{
    /**
     * @var array
     */
    protected $ignoredFormControls = array(
        'radio',
        'checkbox',
        'file',
        'button',
        'submit',
        'reset',
    );

    /**
     * @param  ZendElementInterface $element
     * @return string
     */
    public function render(ZendElementInterface $element)
    {
        if (!($element instanceof ElementInterface)) {
            throw new InvalidArgumentException(
                'Element does not implement ' . ElementerInterface::class
            );
        }

        if (!in_array($element->getAttribute('type'), $this->ignoredFormControls)) {
            $element->addClass('form-control');
        }

        if ($element->getMessages()) {
            $element->addClass('is-invalid');
        }

        return $markup;
    }
}
