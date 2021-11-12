<?php

namespace FormBundle\Form\Admin\Field\Field;

use CommonBundle\Component\Form\FieldsetInterface;
use CommonBundle\Entity\General\Language;

/**
 * Add Dropdown Field
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class TimeSlot extends \CommonBundle\Component\Form\Admin\Fieldset\Tabbable
{
    public function init()
    {
        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'start_date',
                'label'    => 'Start Date',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'     => 'datetime',
                'name'     => 'end_date',
                'label'    => 'End Date',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'DateCompare',
                                'options' => array(
                                    'first_date' => 'start_date',
                                    'format'     => 'd/m/Y H:i',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        parent::init();
    }

    protected function addTab(FieldsetInterface $container, Language $language, $isDefault)
    {
        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'location',
                'label'    => 'Location',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $container->add(
            array(
                'type'     => 'text',
                'name'     => 'extra_info',
                'label'    => 'Extra Information',
                'required' => false,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        $specs['start_date']['required'] = $this->isRequired();
        $specs['end_date']['required'] = $this->isRequired();

        return $specs;
    }

    public function setRequired($required = true)
    {
        return $this->setElementRequired($required);
    }
}
