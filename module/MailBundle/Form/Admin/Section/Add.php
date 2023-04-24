<?php

namespace MailBundle\Form\Admin\Section;

/**
 * Add Section
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\Section';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'name',
                'label'    => 'Name',
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
                'type'     => 'text',
                'name'     => 'attribute',
                'label'    => 'Attribute SendInBlue',
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
                'type'     => 'checkbox',
                'name'     => 'default_value',
                'label'    => 'Default Preference'
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'section_group',
                'label'      => 'Group',
                'required'   => false,
                'attributes' => array(
                    'options' => $this->createGroupsArray(),
                ),
                'options'    => array(
                    'input' => array(
                        'filter' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'mail_add');
    }

    public function createGroupsArray() {
        $groups = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\Section\Group')
            ->findAllQuery()->getResult();

        $groupsArray = array(
            '' => '',
        );
        foreach ($groups as $group){
            $groupsArray[$group->getId()] = $group->getName();
        }
        return $groupsArray;
    }
}