<?php

namespace MailBundle\Form\Admin\MailingList\Entry;

use CommonBundle\Entity\User\Person;
use MailBundle\Entity\MailingList as MailingListEntity;

/**
 * Add MailingList
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class MailingList extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\MailingList\Entry\MailingList';

    /**
     * @var Person The authenticated person
     */
    protected $person = null;

    /**
     * @var MailingListEntity The current list
     */
    protected $list = null;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'select',
                'name'     => 'entry',
                'label'    => 'List',
                'required' => true,
                'options'  => array(
                    'options' => $this->createEntriesArray(),
                    'input'   => array(
                        'validators' => array(
                            array(
                                'name'    => 'EntryMailingList',
                                'options' => array(
                                    'list' => $this->getList(),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'list_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'mail_add',
                ),
            )
        );
    }

    private function createEntriesArray()
    {
        $editor = false;
        foreach ($this->getPerson()->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                $editor = true;
                break;
            }
        }

        $lists = $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\Named')
            ->findBy(array(), array('name' => 'ASC'));

        if (!$editor) {
            $listsArray = array();
            foreach ($lists as $list) {
                if ($list->canBeEditedBy($this->getPerson())) {
                    $listsArray[] = $list;
                }
            }
        } else {
            $listsArray = $lists;
        }

        foreach ($listsArray as $key => $value) {
            $lists = $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Entry\MailingList')
                ->findBy(
                    array(
                        'list'  => $this->getList(),
                        'entry' => $value,
                    )
                );
            if ($value === $this->getList() || count($lists) > 0) {
                unset($listsArray[$key]);
            }
        }

        $lists = array();
        foreach ($listsArray as $list) {
            $lists[$list->getId()] = $list->getName();
        }

        return $lists;
    }

    /**
     * @param  MailingListEntity $list
     * @return self
     */
    public function setList(MailingListEntity $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return MailingListEntity
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}
