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

namespace CommonBundle\Component\Form\View\Helper\Admin;

use CommonBundle\Component\Form\Element\Button;
use CommonBundle\Component\Form\Element\Checkbox;
use CommonBundle\Component\Form\Element\Select;
use CommonBundle\Component\Form\Element\Submit;
use CommonBundle\Component\Form\Element\Textarea;
use CommonBundle\Component\Form\LabelAwareInterface;
use InvalidArgumentException;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormRow as ZendFormRow;

/**
 * View helper to render a form row.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormRow extends \Zend\Form\View\Helper\FormRow
{
    /**
     * @param  ElementInterface $element
     * @param  string           $labelPosition
     * @return string
     */
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();

        if ($labelPosition === null) {
            $labelPosition = $this->labelPosition;
        }

        if (isset($label) && $label != '') {
            $translator = $this->getTranslator();
            if ($translator !== null) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }
        }

        if ($element->getMessages() && $inputErrorClass !== null) {
            if (!in_array($inputErrorClass, explode(' ', $element->getAttribute('class')))) {
                $classes = array($class);
                if ($element->hasAttribute('class')) {
                    $classes = explode(' ', $element->getAttribute('class'));
                    $classes[] = $class;
                }

                $element->setAttribute(
                    'class',
                    implode(' ', $classes)
                );
            }
        }

        if ($this->partial) {
            return $this->view->render(
                $this->partial,
                array(
                    'element'         => $element,
                    'label'           => $label,
                    'labelAttributes' => $this->labelAttributes,
                    'labelPosition'   => $labelPosition,
                    'renderErrors'    => $this->renderErrors,
                )
            );
        }

        if ($this->renderErrors) {
            $elementErrors = $elementErrorsHelper->render($element);
        }

        $labelWrapperClasses = array('label');
        $wrapperClasses = array('row');

        $elementString = $elementHelper->render($element);

        if ($element->getAttribute('type') != 'hidden') {
            $labelAttributes = array();
            if ($element instanceof LabelAwareInterface) {
                if ($element->hasAttribute('required') && $element->getAttribute('required') === true) {
                    $element->addLabelClass('required');
                }

                $labelAttributes = $element->getLabelAttributes();
            }

            if (count($labelAttributes) == 0) {
                $labelAttributes = $this->labelAttributes;
            }

            if (!($element instanceof LabelAwareInterface) || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            if ($this->renderErrors && isset($elementErrors) && !empty($elementErrors)) {
                $elementString .= $elementErrors;
            }

            $labelOpen = $labelHelper->openTag($labelAttributes);
            $labelClose = $labelHelper->closeTag();

            if ($element instanceof Button || $element instanceof Submit) {
                $labelOpen = '';
                $labelClose = '';

                $label = '';
            }

            if (($element instanceof Select && $element->hasAttribute('multiple')) || $element instanceof Textarea) {
                $labelWrapperClasses[] = 'align-top';
            }

            $markup = sprintf(
                '<div class="%s">%s%s%s</div><div class="field">%s</div>',
                implode(' ', $labelWrapperClasses),
                $labelOpen,
                $label,
                $labelClose,
                $elementString
            );
        } else {
            $markup = $elementString;
            if ($this->renderErrors && isset($elementErrors) && !empty($elementErrors)) {
                $markup = $elementString . $elementErrors;
            }

            return $markup;
        }

        return sprintf(
            '<div class="%s">%s</div>',
            implode(' ', $wrapperClasses),
            $markup
        );
    }
}
