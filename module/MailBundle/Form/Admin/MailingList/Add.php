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

namespace MailBundle\Form\Admin\MailingList;

use CommonBundle\Component\Validator\Academic as AcademicValidator,
    MailBundle\Component\Validator\NamedList as NameValidator;

/**
 * Add MailingList
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\MailingList\MailingList';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'name',
            'label'      => 'Name',
            'required'   => true,
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new NameValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

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
            'label'      => 'Admin',
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
                ),
            ),
        ));

        $this->addSubmit('Add', 'mail_add');
    }
}
