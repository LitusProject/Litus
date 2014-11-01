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

namespace CudiBundle\Form\Admin\Sale\Booking;

use CommonBundle\Component\Validator\Typeahead\Person as PersonTypeaheadValidator,
    CudiBundle\Component\Validator\Typeahead\Sale\Article as SaleArticleTypeaheadValidator;

/**
 * Add Booking
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Sale\Booking';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'person',
            'label'      => 'Person',
            'required'   => true,
            'attributes' => array(
                'id'           => 'person',
                'style'        => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        new PersonTypeaheadValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'typeahead',
            'name'       => 'article',
            'label'      => 'Article',
            'required'   => true,
            'attributes' => array(
                'id'           => 'article',
                'style'        => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'validators'  => array(
                        new SaleArticleTypeaheadValidator($this->getEntityManager()),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'amount',
            'label'      => 'Amount',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
            ),
            'options'    => array(
                'input' => array(
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'int',
                        ),
                        array(
                            'name' => 'greaterthan',
                            'options' => array(
                                'min' => 0,
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Add', 'booking_add');
    }
}
