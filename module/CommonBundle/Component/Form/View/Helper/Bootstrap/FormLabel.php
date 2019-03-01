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

use CommonBundle\Component\Form\Element\Checkbox;
use CommonBundle\Component\Form\Element\Radio;
use CommonBundle\Component\Form\ElementInterface;

/**
 * View helper to render a form label.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormLabel extends \Zend\Form\View\Helper\FormLabel
{
    public function __invoke(ElementInterface $element = null, $labelContent = null, $position = null)
    {
        if ($element->getOption('formLayout') === Form::LAYOUT_HORIZONTAL) {
            if (!($element instanceof Checkbox) && !($element instanceof Radio)) {
                if (!$element->hasLabelClass('col-form-label')) {
                    $element->addLabelClass('col-form-label');
                }
            }
        }

        return parent::__invoke($element, $labelContent, $position);
    }
}
