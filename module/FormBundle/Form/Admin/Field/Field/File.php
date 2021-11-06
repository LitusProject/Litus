<?php

namespace FormBundle\Form\Admin\Field\Field;

/**
 * Add File Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class File extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'    => 'text',
                'name'    => 'max_size',
                'label'   => 'Maximum Size (in MB)',
                'value'   => 5,
                'options' => array(
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

    public function setRequired($required = true)
    {
        return $this->setElementRequired($required);
    }
}
