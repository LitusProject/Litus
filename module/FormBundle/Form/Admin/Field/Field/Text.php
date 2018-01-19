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

namespace FormBundle\Form\Admin\Field\Field;

/**
 * Add Text Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Text extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'multiline',
            'label'      => 'Multiline',
            'attributes' => array(
                'data-help' => 'Allow multiple lines in the field (textarea).',
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'charsperline',
            'label'      => 'Max. characters per line (or Infinite)',
            'attributes' => array(
                'data-help' => 'The maximum numbers of characters on one line. Zero is infinite.',
            ),
            'options'    => array(
                'input' => array(
                    'allow_empty'       => false,
                    'continue_if_empty' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'notempty',
                            'options' => array(
                                'null',
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'lines',
            'label'      => 'Max. number of lines (Multiline fields only)',
            'attributes' => array(
                'data-help' => 'The maximum numbers of lines. Zero is infinite.',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'digits',
                        ),
                    ),
                ),
            ),
        ));
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if ($this->get('charsperline')->getValue() != '') {
            $specs['charsperline']['validators'][] = array(
                'name' => 'digits',
            );
        }

        $multilineValue = $this->get('multiline')->getValue();
        $lineValue = $this->get('lines')->getValue();

        $specs['charsperline']['validators'][] = array(
            'name' => 'form_text_field',
            'options' => array(
                'multiline' => !empty($multilineValue) ? $this->get('multiline')->getValue() : false,
                'lines' => !empty($lineValue) ? $this->get('lines')->getValue() : null,
            ),
        );

        return $specs;
    }

    public function setRequired($required = true)
    {
        return $this->setElementRequired($required);
    }
}
