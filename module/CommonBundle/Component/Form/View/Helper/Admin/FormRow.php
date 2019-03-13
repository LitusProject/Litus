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
use CommonBundle\Component\Form\Element\Select;
use CommonBundle\Component\Form\Element\Submit;
use CommonBundle\Component\Form\Element\Textarea;
use CommonBundle\Component\Form\LabelAwareInterface;
use RuntimeException;
use Zend\Form\ElementInterface;

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

        if ($label != '') {
            $translator = $this->getTranslator();
            if ($translator !== null) {
                $label = $translator->translate($label, $this->getTranslatorTextDomain());
            }
        }

        if ($inputErrorClass !== null && count($element->getMessages()) > 0) {
            if (!in_array($inputErrorClass, explode(' ', $element->getAttribute('class')))) {
                $classes = array($inputErrorClass);
                if ($element->hasAttribute('class')) {
                    $classes = explode(' ', $element->getAttribute('class'));
                    $classes[] = $inputErrorClass;
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

        $elementErrors = '';
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

            if (!($element instanceof LabelAwareInterface) || $element->getLabelOption('disable_html_escape') === null) {
                $label = $escapeHtmlHelper($label);
            }

            if ($this->renderErrors && $elementErrors != '') {
                $elementString .= $elementErrors;
            }

            $labelOpen = $labelHelper->openTag($labelAttributes);
            $labelClose = $labelHelper->closeTag();

            if ($element instanceof Button || $element instanceof Submit) {
                $labelOpen = '';
                $labelClose = '';

                $label = '';
            }

            if ($element instanceof LabelAwareInterface && $element->getLabelOption('label_position') !== null) {
                $labelPosition = $element->getLabelOption('label_position');
            }

            if (($element instanceof Select && $element->hasAttribute('multiple')) || $element instanceof Textarea) {
                $labelWrapperClasses[] = 'align-top';
            }

            $labelString = '';
            if ($label != '') {
                $labelString = sprintf(
                    '<div class="%s">%s%s%s</div>',
                    implode(' ', $labelWrapperClasses),
                    $labelOpen,
                    $label,
                    $labelClose
                );
            }

            $markup = '';
            switch ($labelPosition) {
                case self::LABEL_APPEND:
                    $markup = sprintf(
                        '<div class="field">%s</div>%s',
                        $elementString,
                        $labelString
                    );
                    break;

                case self::LABEL_PREPEND:
                    $markup = sprintf(
                        '%s<div class="field">%s</div>',
                        $labelString,
                        $elementString
                    );
                    break;

                default:
                    throw new RuntimeException(
                        'Form element specified invalid label position'
                    );
            }

            return sprintf(
                '<div class="%s">%s</div>',
                implode(' ', $wrapperClasses),
                $markup
            );
        } else {
            $markup = $elementString;
            if ($this->renderErrors && strlen($elementErrors) > 0) {
                $markup = $elementString . $elementErrors;
            }

            return $markup;
        }
    }
}
