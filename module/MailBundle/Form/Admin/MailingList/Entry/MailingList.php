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

namespace MailBundle\Form\Admin\MailingList\Entry;

use CommonBundle\Entity\User\Person,
    MailBundle\Component\Validator\Entry\MailingList as MailingListEntryValidator,
    MailBundle\Entity\MailingList as MailingListEntity;

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
    protected $_person = null;

    /**
     * @var MailingListEntity The current list
     */
    protected $_list = null;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'select',
            'name'       => 'entry',
            'label'      => 'List',
            'required'   => true,
            'options'    => array(
                'options' => $this->_createEntriesArray(),
                'input' => array(
                    'validators' => array(
                        new MailingListEntryValidator($this->getEntityManager(), $this->getList()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'submit',
            'name'       => 'list_add',
            'value'      => 'Add',
            'attributes' => array(
                'class' => 'mail_add',
            ),
        ));
    }

    private function _createEntriesArray()
    {
        $editor = false;
        foreach ($this->getPerson()->getFlattenedRoles() as $role) {
            if ($role->getName() == 'editor') {
                $editor = true;
                break;
            }
        }

        $lists =  $this->getEntityManager()
            ->getRepository('MailBundle\Entity\MailingList\Named')
            ->findBy(array(), array('name' => 'ASC'));

        if (!$editor) {
            $listsArray = array();
            foreach ($lists as $list) {
                if ($list->canBeEditedBy($this->getPerson()))
                    $listsArray[] = $list;
            }
        } else {
            $listsArray = $lists;
        }

        foreach ($listsArray as $key => $value) {
            $lists = $this->getEntityManager()
                ->getRepository('MailBundle\Entity\MailingList\Entry\MailingList')
                ->findBy(
                    array(
                        'list' => $this->getList(),
                        'entry' => $value
                    )
                );
            if ($value === $this->getList() || count($lists) > 0)
                unset($listsArray[$key]);
        }

        $lists = array();
        foreach ($listsArray as $list)
            $lists[$list->getId()] = $list->getName();

        return $lists;
    }

    /**
     * @param  MailingListEntity $list
     * @return self
     */
    public function setList(MailingListEntity $list)
    {
        $this->_list = $list;

        return $this;
    }

    /**
     * @return MailingListEntity
     */
    public function getList()
    {
        return $this->_list;
    }

    /**
     * @param  Person $person
     * @return self
     */
    public function setPerson(Person $person)
    {
        $this->_person = $person;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->_person;
    }
}
