<?php

namespace SyllabusBundle\Form\Admin\Study\ModuleGroup;

use SyllabusBundle\Entity\Study\ModuleGroup;

/**
 * Add ModuleGroup
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Study\ModuleGroup';

    /**
     * @var ModuleGroup|null
     */
    protected $moduleGroup = null;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'title',
                'label'      => 'Title',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'external_id',
                'label'    => 'External Id',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                            array(
                                'name'    => 'StudyModuleGroupExternalId',
                                'options' => array(
                                    'exclude' => $this->moduleGroup,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'phase',
                'label'    => 'Phase',
                'required' => true,
                'options'  => array(
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

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'language',
                'label'    => 'Language',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'mandatory',
                'label' => 'Mandatory',
            )
        );

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'parent',
                'label'      => 'Parent',
                'required'   => false,
                'attributes' => array(
                    'id'    => 'parent',
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'TypeaheadStudyModuleGroup',
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  ModuleGroup $moduleGroup
     * @return self
     */
    public function setModuleGroup(ModuleGroup $moduleGroup)
    {
        $this->moduleGroup = $moduleGroup;

        return $this;
    }
}
