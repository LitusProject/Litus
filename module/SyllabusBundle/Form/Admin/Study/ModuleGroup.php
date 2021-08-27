<?php

namespace SyllabusBundle\Form\Admin\Study;

/**
 * Add Module Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ModuleGroup extends \CommonBundle\Component\Form\Fieldset
{
    public function init()
    {
        parent::init();

        $this->setLabel('Module Group');

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'module_group',
                'required'   => false,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'TypeaheadStudyModuleGroup'),
                        ),
                    ),
                ),
            )
        );
    }
}
