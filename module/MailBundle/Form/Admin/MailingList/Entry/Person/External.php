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

namespace MailBundle\Form\Admin\MailingList\Entry\Person;

use MailBundle\Component\Validator\Entry\External as ExternalEntryValidator,
    MailBundle\Entity\MailingList;

/**
 * Add External
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class External extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\MailingList\Entry\Person\External';

    /**
     * @var MailingList
     */
    private $_list;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'first_name',
            'label'      => 'First Name',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'last_name',
            'label'      => 'Last Name',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'email_address',
            'label'      => 'Email',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'emailaddress'),
                        new ExternalEntryValidator($this->getEntityManager(), $this->getList())
                    )
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'submit',
            'name'       => 'external_add',
            'value'      => 'Add',
            'attributes' => array(
                'class' => 'mail_add',
            ),
        ));
    }

    /**
     * @param  MailingList $list
     * @return self
     */
    public function setList(MailingList $list)
    {
        $this->_list = $list;

        return $this;
    }

    /**
     * @return MailingList
     */
    public function getList()
    {
        return $this->_list;
    }
}
