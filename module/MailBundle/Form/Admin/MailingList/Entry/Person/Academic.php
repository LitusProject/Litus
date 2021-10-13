<?php

namespace MailBundle\Form\Admin\MailingList\Entry\Person;

use MailBundle\Entity\MailingList;

/**
 * Add Academic
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Academic extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\MailingList\Entry\Person\Academic';

    /**
     * @var MailingList
     */
    private $list;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'EntryAcademic',
                                'options' => array(
                                    'list' => $this->getList(),
                                ),
                            ),
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'academic_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'mail_add',
                ),
            )
        );
    }

    /**
     * @param  MailingList $list
     * @return self
     */
    public function setList(MailingList $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * @return MailingList
     */
    public function getList()
    {
        return $this->list;
    }
}
