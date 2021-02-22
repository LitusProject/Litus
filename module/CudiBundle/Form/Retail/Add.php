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

namespace CudiBundle\Form\Retail;

/**
 * Add Retail
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    protected $hydrator = 'CudiBundle\Hydrator\Retail';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'article',
                'label'      => 'Article',
                'required'   => true,
                'attributes' => array(
                    'id'    => 'article',
                    'style' => 'width: 400px;',
                ),
                'options' => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadRetail'),
                        ),
                    ),
                ),
            )
        ); // TODO validator must check for book (and not e.g. pen)

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'price',
                'label'      => 'Price',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 75px;',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'Price'),
                            array('name' => 'MaximalRetailPrice')
                        ),
                    ),
                ),
            )
        ); // TODO check price validator

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'anonymous',
                'label'      => 'Anonymous',
                'attributes' => array(
                    'data-help' => 'If this flag is enabled, the owner\'s name will not be visible to the buyers.',
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'comment',
                'label'    => 'Comment',
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Add', 'retail_add');
    }
}
