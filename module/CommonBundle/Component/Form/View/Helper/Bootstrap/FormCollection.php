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

/**
 * View helper to render a form collection.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormCollection extends \Zend\Form\View\Helper\FormCollection
{
    protected $wrapper = '<fieldset%4$s>%2$s%1$s%3$s</fieldset></div>';

    public function __invoke(ElementInterface $element = null, $wrap = true)
    {
        $formLayout = $element->getOption('formLayout');
        if ($formLayout !== null) {
            foreach ($element->getElements() as $fieldsetElement) {
                $fieldsetElement->setOption('formLayout', $formLayout);
            }

            foreach ($element->getFieldsets() as $fieldsetFieldset) {
                $fieldsetFieldset->setOption('formLayout', $formLayout);
            }
        }

        $this->wrapper = sprintf('<div class="form-group">%s</div>', $this->wrapper);
        if ($formLayout === Form::LAYOUT_HORIZONTAL) {
            $this->wrapper = sprintf('<div class="form-group row">%s</div>', $this->wrapper);
        }

        return parent::__invoke($element, $wrap);
    }
}
