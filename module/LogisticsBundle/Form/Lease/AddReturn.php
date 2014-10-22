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

namespace LogisticsBundle\Form\Lease;

use CommonBundle\Component\Validator\Price as PriceValidator,
    LogisticsBundle\Component\Validator\Typeahead\Lease as LeaseTypeaheadValidator;

/**
 * The form used to register a returned item.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class AddReturn extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'returnItem',
            'label'      => 'Item',
            'options' => array(
                'input' => array(
                    'validators' => array(
                        new LeaseTypeaheadValidator($this->getEntityManager(), true),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'returned_amount',
            'label'      => 'Amount',
            'required'   => true,
            'attributes' => array(
                'id' => 'returned_amount',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'Digits'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'returned_by',
            'label'      => 'Returned By',
            'required'   => true,
            'attributes' => array(
                'id'           => 'returned_by',
                'autocomplete' => false,
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'returned_pawn',
            'label'      => 'Returned Pawn',
            'required'   => true,
            'attributes' => array(
                'id' => 'returned_pawn',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator(),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'textarea',
            'name'       => 'comment',
            'label'      => 'Comment',
            'attributes' => array(
                'rows' => 3,
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Return', 'btn btn-primary', 'return');
    }
}
