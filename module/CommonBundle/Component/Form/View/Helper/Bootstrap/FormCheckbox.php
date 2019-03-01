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
use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\FormLabel;

/**
 * View helper to render a form checkbox.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormCheckbox extends \Zend\Form\View\Helper\FormCheckbox
{
    protected $labelHelper;

    public function render(ElementInterface $element)
    {
        if (!preg_match('/custom-control-input/i', $element->getAttribute('class'))) {
            $element->addClass('custom-control-input');
        }

        if ($element instanceof LabelAwareInterface) {
            $labelAttributes = $element->getLabelAttributes();
            if (array_key_exists('class', $labelAttributes)) {
                if (!preg_match('/custom-control-label/i', $labelAttributes['class'])) {
                    $labelAttributes['class'] = trim($labelAttributes['class'] . ' custom-control-label');
                }
            } else {
                $labelAttributes['class'] = 'custom-control-label';
            }

            $element->setLabelAttributes($labelAttributes);
        }

        return sprintf(
            '<div class="custom-control custom-checkbox">%s%s</div>',
            parent::render($element),
            $this->getLabelHelper()($element)
        );
    }

    protected function getLabelHelper()
    {
        if ($this->labelHelper !== null) {
            return $this->labelHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->labelHelper = $this->view->plugin('form_label');
        }

        if (!($this->labelHelper instanceof FormLabel)) {
            $this->labelHelper = new FormLabel();
        }

        if ($this->hasTranslator()) {
            $this->labelHelper->setTranslator(
                $this->getTranslator(),
                $this->getTranslatorTextDomain()
            );
        }

        return $this->labelHelper;
    }
}
