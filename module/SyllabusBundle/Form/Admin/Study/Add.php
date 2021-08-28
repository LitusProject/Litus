<?php

namespace SyllabusBundle\Form\Admin\Study;

use SyllabusBundle\Entity\Study;

/**
 * Add Study
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Study';

    /**
     * @var Study|null
     */
    protected $study = null;

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
                                'name'    => 'StudyExternalId',
                                'options' => array(
                                    'exclude' => $this->study,
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
                            array('name' => 'int'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'collection',
                'name'    => 'module_groups',
                'label'   => 'Module Groups',
                'options' => array(
                    'count'                  => 0,
                    'should_create_template' => true,
                    'allow_add'              => true,
                    'target_element'         => array(
                        'type' => 'syllabus_study_module-group',
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Study $study
     * @return self
     */
    public function setStudy(Study $study)
    {
        $this->study = $study;

        return $this;
    }
}
