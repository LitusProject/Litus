<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace MailBundle\Form\Admin\MailingList\Entry;

use CommonBundle\Component\Form\Admin\Element\Collection,
    CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add MailingList
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailingList extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    protected $_entityManager = null;

    /**
     * @var \CommonBundle\Entity\User\Person The authenticated person
     */
    protected $_authenticatedPerson = null;

    /**
     * @var \MailBundle\Entity\MailingList The current list
     */
    protected $_currentList = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\User\Person $authenticatedPerson The authenticated person
     * @param \MailBundle\Entity\MailingList $currentList The current list
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Person $authenticatedPerson, \MailBundle\Entity\MailingList $currentList, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_authenticatedPerson = $authenticatedPerson;
        $this->_currentList = $currentList;

        $list = new Collection('list');
        $list->setLabel('Add List');
        $this->add($list);

        $field = new Select('entry');
        $field->setLabel('List')
            ->setRequired(true)
            ->setAttribute('options', $this->_createEntriesArray());
        $list->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'mail_add');
        $list->add($field);
    }

    private function _createEntriesArray()
    {
        $editor = false;
        foreach ($this->_authenticatedPerson->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                $editor = true;
                break;
            }
        }

        $lists =  $this->_entityManager
            ->getRepository('MailBundle\Entity\MailingList\Named')
            ->findBy(array(), array('name' => 'ASC'));

        if (!$editor) {
            $listsArray = array();
            foreach ($lists as $list) {
                if ($list->canBeEditedBy($this->_authenticatedPerson))
                    $listsArray[] = $list;
            }
        } else {
            $listsArray = $lists;
        }

        foreach ($listsArray as $key => $value) {
            $lists = $this->_entityManager
                ->getRepository('MailBundle\Entity\MailingList\Entry\MailingList')
                ->findBy(
                    array(
                        'list' => $this->_currentList,
                        'entry' => $value
                    )
                );

            if ($value === $this->_currentList || count($lists) > 0)
                unset($listsArray[$key]);
        }

        foreach ($listsArray as $list)
            $lists[$list->getId()] = $list->getName();

        return $lists;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'entry',
                    'required' => true,
                )
            )
        );

        return $inputFilter;
    }
}
