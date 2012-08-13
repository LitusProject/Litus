<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Form\Admin\Role;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\QueryBuilder,
    Zend\Form\Form,
    Zend\Form\Element\Multiselect,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text;

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
     * @param mixed $opts The form's options
     */
    public function __construct(EntityManager $entityManager, $opts = null)
    {
        parent::__construct($opts);

        $this->_entityManager = $entityManager;

        $field = new Text('name');
        $field->setLabel('Name')
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Multiselect('parents');
        $field->setLabel('Parents')
            ->setMultiOptions($this->_createParentsArray())
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Multiselect('actions');
        $field->setLabel('Allowed Actions')
            ->setMultiOptions($this->_createActionsArray())
            ->setAttrib('style', 'height: 300px;')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Add')
            ->setAttrib('class', 'groups_add')
            ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);
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
        foreach ($roles as $role) {
            $parents[$role->getName()] = $role->getName();
        }

        ksort($parents);

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
        $query = new QueryBuilder(
            $this->_entityManager
        );
        $query->select('r')
            ->from('CommonBundle\Entity\Acl\Resource', 'r')
            ->where('r.parent IS NULL');
        $resources = $query->getQuery()->useResultCache(true)->getResult();

        $actions = array();
        foreach ($resources as $resource) {
            $resourceActions = $resource->getActions($this->_entityManager);
            foreach ($resourceActions as $resourceAction) {
                $actions[$resource->getName()][$resourceAction->getId()] = $resourceAction->getName();
            }

            $resourceChildren = $resource->getChildren($this->_entityManager);
            foreach ($resourceChildren as $resourceChild) {
                $childActions = $resourceChild->getActions($this->_entityManager);
                foreach ($childActions as $childAction) {
                    $actions[$resourceChild->getName()][$childAction->getId()] = $childAction->getName();
                }

                asort($actions[$resourceChild->getName()]);
            }
        }

        ksort($actions);

        return $actions;
    }
}
