<?php

namespace SyllabusBundle\Form\Admin\Poc;

use SyllabusBundle\Entity\Group;

/**
 * Add Poc
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Poc';

    /**
     * @var Group|null
     */
    protected $pocGroup = null;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'person',
                'label'      => 'POC\'er',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'person',
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'user_add');
    }

    /**
     * @param  Group $pocGroup
     * @return self
     */
    public function setPocGroup(Group $pocGroup)
    {
        $this->pocGroup = $pocGroup;

        return $this;
    }

    /**
     * @return Group
     */
    public function getPocGroup(Group $pocGroup)
    {
        return $this->pocGroup;
    }
}
