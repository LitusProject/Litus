<?php

namespace Admin\Form\Role;

use \Doctrine\ORM\QueryBuilder;

use \Litus\Application\Resource\Doctrine as DoctrineResource;
use \Litus\Form\Admin\Decorator\ButtonDecorator;
use \Litus\Form\Admin\Decorator\FieldDecorator;

use \Zend\Form\Form;
use \Zend\Form\Element\Multiselect;
use \Zend\Form\Element\Submit;
use \Zend\Form\Element\Text;
use \Zend\Registry;

class Add extends \Litus\Form\Admin\Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

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
            ->setRequired()
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

    private function _createParentsArray()
    {
        $roles = Registry::get('EntityManager')
            ->getRepository('Litus\Entity\Acl\Role')
            ->findAll();

        $parents = array();
        foreach ($roles as $role) {
            $parents[$role->getName()] = $role->getName();
        }
        return $parents;
    }

    private function _createActionsArray()
    {
        $query = new QueryBuilder(
            Registry::get(DoctrineResource::REGISTRY_KEY)
        );
        $query->select('r')
            ->from('Litus\Entity\Acl\Resource', 'r')
            ->where('r.parent IS NULL');
        $resources = $query->getQuery()->useResultCache(true)->getResult();

        $actions = array();
        foreach ($resources as $resource) {
            $resourceActions = $resource->getActions();
            foreach ($resourceActions as $resourceAction) {
                $actions[$resource->getName()][$resourceAction->getId()] = $resourceAction->getName();
            }

            $resourceChildren = $resource->getChildren();
            foreach ($resourceChildren as $resourceChild) {
                $childActions = $resourceChild->getActions();
                foreach ($childActions as $childAction) {
                    $actions[$resourceChild->getName()][$childAction->getId()] = $childAction->getName();
                }
            }
        }

        return $actions;
    }
}