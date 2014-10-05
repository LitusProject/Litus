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
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Form\Admin\Group;

use MailBundle\Component\Validator\MultiMail as MultiMailValidator,
    SyllabusBundle\Component\Validator\Group\Name as NameValidator,
    SyllabusBundle\Entity\Group;

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

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'name',
            'label'      => 'Name',
            'required'   => true,
            'attributes' => array(
                'size' => 70,
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NameValidator($this->getEntityManager(), $this->group),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'cv_book',
            'label' => 'Show in CV Book',
        ));

        $this->add(array(
            'type'    => 'textarea',
            'name'    => 'extra_members',
            'label'   => 'Extra Members',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'    => 'textarea',
            'name'    => 'excluded_members',
            'label'   => 'Excluded Members',
            'options' => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                ),
            ),
        ));

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
}
