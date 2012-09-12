<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Admin\Role;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Validator\Role as RoleValidator,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Role
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired();
        $this->add($field);

        $field = new Select('parents');
        $field->setLabel('Parents')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_createParentsArray());
        $this->add($field);

        $field = new Select('actions');
        $field->setLabel('Allowed Actions')
            ->setAttribute('multiple', true)
            ->setAttribute('options', $this->_createActionsArray())
            ->setAttribute('style', 'height: 300px;');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'group_add');
        $this->add($field);
    }

    /**
     * Returns an array that has all the roles, so that they are available in the
     * parents multiselect.
     *
     * @return array
     */
    private function _createParentsArray()
    {
        $roles = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Role')
            ->findAll();

        $parents = array();
        foreach ($roles as $role)
            $parents[$role->getName()] = $role->getName();

        asort($parents);

        return $parents;
    }

    /**
     * Returns an array that has all the actions that are currently in the database
     * so that we can assign some to this role.
     *
     * @return array
     */
    private function _createActionsArray()
    {
        $resources = $this->_entityManager
            ->getRepository('CommonBundle\Entity\Acl\Resource')
            ->findByParent(null);

        $actions = array();
        foreach ($resources as $resource) {
            $resourceChildren = $resource->getChildren($this->_entityManager);
            foreach ($resourceChildren as $resourceChild) {
                $childActions = $resourceChild->getActions($this->_entityManager);
                $actions[$resourceChild->getName()] = array(
                    'label' => $resourceChild->getName(),
                    'options' => array()
                );
                foreach ($childActions as $childAction) {
                    $actions[$resourceChild->getName()]['options'][$childAction->getId()] = $childAction->getName();
                }

                asort($actions[$resourceChild->getName()]['options']);
            }
        }

        ksort($actions);

        return $actions;
    }

    public function getInputFilter()
    {
        if ($this->_inputFilter == null) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'name',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            new RoleValidator($this->_entityManager),
                        ),
                    )
                )
            );
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
}
