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

use Zend\Form\ElementInterface;

/**
 * View helper to render a form reset.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormSubmit extends \Zend\Form\View\Helper\FormSubmit
{
    public function render(ElementInterface $element, $buttonContent = null)
    {
        if (!preg_match('/btn-[a-z]+/i', $element->getAttribute('class'))) {
            $element->setAttribute('class', trim('btn-primary ' . $element->getAttribute('class')));
        }

        if (!preg_match('/btn(?!-)/i', $element->getAttribute('class'))) {
            $element->setAttribute('class', trim('btn ' . $element->getAttribute('class')));
        }

        return parent::render($element, $buttonContent);
    }
}
