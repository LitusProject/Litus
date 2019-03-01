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

use CommonBundle\Component\Form\Element\Button;
use CommonBundle\Component\Form\Element\Checkbox;
use CommonBundle\Component\Form\Element\Submit;
use CommonBundle\Component\Form\ElementInterface;
use CommonBundle\Component\Form\LabelAwareInterface;
use Zend\Form\View\Helper\FormRow as ZendFormRow;

/**
 * View helper to render a form row.
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class FormRow extends \Zend\Form\View\Helper\FormRow
{
    protected $inputErrorClass = 'is-invalid';

    public function render(ElementInterface $element, $labelPosition = null)
    {
        if ($element instanceof Checkbox) {
            $element->setLabelOption(
                'label_position',
                $element->getLabelOption('label_position') ?? ZendFormRow::LABEL_APPEND
            );
        }

        $formLayout = $element->getOption('formLayout');

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
            if (!$element->hasClass($inputErrorClass)) {
                $element->addClass($inputErrorClass);
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

        $elementString = $elementHelper->render($element);

        $wrapper = '<div class="form-group">%s</div>';
        if ($formLayout == Form::LAYOUT_HORIZONTAL) {
            $wrapper = '<div class="form-group row">%s</div>';
        }

        $type = $element->getAttribute('type');
        if (isset($label) && $label != '' && $type != 'hidden') {
            $labelAttributes = array();
            if ($element instanceof LabelAwareInterface) {
                $labelAttributes = $element->getLabelAttributes();
            }

            if (!($element instanceof LabelAwareInterface) || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            if (count($labelAttributes) == 0) {
                $labelAttributes = $this->labelAttributes;
            }

            if ($this->renderErrors && isset($elementErrors) && !empty($elementErrors)) {
                $elementString .= $elementErrors;
            }

            if ($type == 'reset' || $type == 'submit') {
                return $elementString;
            } elseif ($type === 'multi_checkbox' || $type === 'radio') {
                $elementString = sprintf($wrapper, $elementString);
                return sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $elementString
                );
            } else {
                $labelOpen = $labelHelper->openTag($labelAttributes);
                $labelClose = $labelHelper->closeTag();

                if ($element->hasAttribute('id') && $element instanceof LabelAwareInterface && !$element->getLabelOption('always_wrap')) {
                    $labelOpen = '';
                    $labelClose = '';

                    $label = $labelHelper->openTag($element) . $label . $labelHelper->closeTag();
                }

                if ($label !== '' && !$element->hasAttribute('id') || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))) {
                    $label = '<span>' . $label . '</span>';
                }

                if ($element instanceof Button || $element instanceof Submit || $element instanceof Checkbox) {
                    $labelOpen = '';
                    $labelClose = '';

                    $label = '';
                }

                if ($element instanceof LabelAwareInterface && $element->getLabelOption('label_position')) {
                    $labelPosition = $element->getLabelOption('label_position');
                }

                if ($formLayout === Form::LAYOUT_HORIZONTAL) {
                    $columnSize = $element->getOption('column-size') ?? 'sm-10';
                    $elementString = sprintf('<div class="col-%s">%s</div>', $columnSize, $elementString);
                }

                $markup = $labelOpen . $label . $elementString . $labelClose;
                if ($labelPosition == self::LABEL_APPEND) {
                    $markup = $labelOpen . $elementString . $label . $labelClose;
                }
            }
        } else {
            $markup = $elementString;
            if ($this->renderErrors && isset($elementErrors) && !empty($elementErrors)) {
                $markup = $elementString . $elementErrors;
            }

            return $markup;
        }

        return sprintf($wrapper, $markup);
    }
}
