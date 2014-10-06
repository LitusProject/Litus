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

namespace CudiBundle\Form\Admin\Stock;

/**
 * Stock Select options
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SelectOptions extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(array(
            'type'       => 'radio',
            'name'       => 'articles',
            'label'      => 'Articles',
            'required'   => true,
            'value'      => 'all',
            'attributes' => array(
                'options' => array(
                    'all'      => 'All',
                    'internal' => 'Internal',
                    'external' => 'External',
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'radio',
            'name'       => 'order',
            'label'      => 'Order',
            'required'   => true,
            'value'      => 'barcode',
            'attributes' => array(
                'options' => array(
                    'barcode' => 'Barcode',
                    'title'   => 'Title',
                ),
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'in_stock',
            'label' => 'Only In Stock',
        ));

        $this->addSubmit('Select', 'view', 'select');
    }
}
