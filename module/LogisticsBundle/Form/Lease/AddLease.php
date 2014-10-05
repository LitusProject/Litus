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
    LogisticsBundle\Component\Validator\LeaseValidator;

/**
 * The form used to add a new Lease.
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class AddLease extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'text',
            'name'       => 'item',
            'label'      => 'Item',
            'attributes' => array(
                'autocomplete' => false,
                'class'        => 'js-item-search',
            ),
        ));

        $this->add(array(
            'type'       => 'hidden',
            'name'       => 'barcode',
            'attributes' => array(
                'class' => 'js-item-barcode',
            ),
            'options'    => array(
                'input' => array(
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'barcode',
                            'options' => array(
                                'adapter'     => 'Ean12',
                                'useChecksum' => false,
                            ),
                        ),
                        new LeaseValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'     => 'text',
            'name'     => 'leased_amount',
            'label'    => 'Amount',
            'required' => true,
            'options'  => array(
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
            'name'       => 'leased_to',
            'label'      => 'Leased To',
            'required'   => true,
            'attributes' => array(
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
            'type'     => 'text',
            'name'     => 'leased_pawn',
            'label'    => 'Received Pawn',
            'required' => true,
            'options'  => array(
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

        $this->addSubmit('Lease', 'btn btn-primary', 'lease');
    }
}
