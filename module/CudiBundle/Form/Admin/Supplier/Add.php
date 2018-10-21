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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Supplier;

use CudiBundle\Entity\Supplier;

/**
 * Add Supplier
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Supplier';

    public function init()
    {
        parent::init();

        $this->add(array(
            'type'     => 'text',
            'name'     => 'name',
            'label'    => 'Name',
            'required' => true,
            'options'  => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'phone_number',
            'label'      => 'Phone Number',
            'attributes' => array(
                'placeholder' => '+CCAAANNNNNN',
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'PhoneNumber'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'contact',
            'label' => 'Contact',
        ));

        $this->add(array(
            'type'  => 'common_address_add',
            'name'  => 'address',
            'label' => 'Address',
        ));

        $this->add(array(
            'type'  => 'text',
            'name'  => 'vat_number',
            'label' => 'VAT Number',
        ));

        $this->add(array(
            'type'       => 'select',
            'name'       => 'template',
            'label'      => 'Template',
            'required'   => true,
            'attributes' => array(
                'options' => Supplier::$possibleTemplates,
            ),
        ));

        $this->addSubmit('Add', 'supplier_add');
    }
}
