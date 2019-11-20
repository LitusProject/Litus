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

namespace SyllabusBundle\Form\Admin\Group;

use SyllabusBundle\Entity\Group;

/**
 * Add Group
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'SyllabusBundle\Hydrator\Group';

    /**
     * @var Group|null
     */
    protected $group = null;
    /**
     * @bool isPocGroup|null
     */
    protected $isPocGroup = 0;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'name',
                'label'      => 'Name',
                'required'   => true,
                'attributes' => array(
                    'size' => 70,
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'GroupName',
                                'options' => array(
                                    'exclude' => $this->group,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'cv_book',
                'label' => 'Show in CV Book',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'poc_group',
                'label' => 'Is POC Group',
                'value' => $this->isPocGroup,
                'attributes' => array(
                    'disabled' => $this->isPocGroup,
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'extra_members',
                'label'   => 'Extra Members',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'MultiMail'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'    => 'textarea',
                'name'    => 'excluded_members',
                'label'   => 'Excluded Members',
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'MultiMail'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'add');
    }

    /**
     * @param  Group $group
     * @return self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    public function setIsPocGroup($isPocGroup)
    {
        $this->isPocGroup = $isPocGroup;

        return $this;
    }
}
