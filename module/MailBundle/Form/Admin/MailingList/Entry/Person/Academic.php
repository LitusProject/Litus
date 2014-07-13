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

use CommonBundle\Component\Validator\Academic as AcademicValidator,
    MailBundle\Component\Validator\Entry\Academic as AcademicEntryValidator,
    MailBundle\Entity\MailingList;

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
    private $_list;

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'person_id',
            'required'   => true,
            'attributes' => array(
                'id' => 'personId',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new AcademicValidator(
                            $this->getEntityManager(),
                            array(
                                'byId' => true,
                            )
                        )
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'person_name',
            'label'      => 'Name',
            'required'   => true,
            'attributes' => array(
                'id'           => 'personSearch',
                'autocomplete' => 'off',
                'data-provide' => 'typeahead',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new AcademicEntryValidator($this->getEntityManager(), $this->getList()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'submit',
            'name'       => 'academic_add',
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
