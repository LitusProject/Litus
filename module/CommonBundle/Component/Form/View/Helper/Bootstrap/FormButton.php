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
 * View helper to render a form button.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormButton extends \Zend\Form\View\Helper\FormButton
{
    /**
     * @param  ZendElementInterface $element
     * @param  string               $buttonContent
     * @return string
     */
    public function render(ZendElementInterface $element, $buttonContent = null)
    {
        if (!($element instanceof ElementInterface)) {
            throw new InvalidArgumentException(
                'Element does not implement ' . ElementInterface::class
            );
        }

        if ($buttonContent === null) {
            $buttonContent = $element->getLabel();

            if ($buttonContent === null) {
                $buttonContent = $element->getValue();
            }

            $translator = $this->getTranslator();
            if ($buttonContent !== null && $translator !== null) {
                $buttonContent = $translator->translate(
                    $buttonContent,
                    $this->getTranslatorTextDomain()
                );
            }
        }

        $element->addClass('btn');

        if (!preg_match('/btn-[a-z]+/i', $element->getAttribute('class'))) {
            $element->addClass('btn-primary');
        }

        return parent::render($element, $buttonContent);
    }
}
