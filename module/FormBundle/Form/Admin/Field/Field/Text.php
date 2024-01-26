<?php

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

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'multiline',
                'label'      => 'Multiline',
                'attributes' => array(
                    'data-help' => 'Allow multiple lines in the field (textarea).',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'charsperline',
                'label'      => 'Maximum Characters per Line (or Infinite)',
                'attributes' => array(
                    'data-help' => 'The maximum numbers of characters on one line. Zero is infinite.',
                ),
                'options'    => array(
                    'input' => array(
                        'allow_empty'       => false,
                        'continue_if_empty' => true,
                        'filters'           => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators'        => array(
                            array(
                                'name'    => 'NotEmpty',
                                'options' => array(
                                    'null',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'lines',
                'label'      => 'Maximum Number of Lines (Multiline Fields Only)',
                'attributes' => array(
                    'data-help' => 'The maximum numbers of lines. Zero is infinite.',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Int'),
                        ),
                    ),
                ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if ($this->get('charsperline')->getValue() != '') {
            $specs['charsperline']['validators'][] = array(
                'name' => 'Int',
            );
        }

        $specs['charsperline']['validators'][] = array(
            'name'    => 'TextField',
            'options' => array(
                'multiline' => $this->get('multiline')->getValue() ?? false,
                'lines'     => $this->get('lines')->getValue(),
            ),
        );

        return $specs;
    }

    public function setRequired($required = true)
    {
        return $this->setElementRequired($required);
    }
}
