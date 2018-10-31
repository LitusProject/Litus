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

namespace CudiBundle\Form\Admin\SpecialAction\Irreeel;

/**
 * Assign Ir.Reëel
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Assign extends \CommonBundle\Component\Form\Admin\Form
{
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
                            array('name' => 'TypeaheadSaleArticle'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'only_cudi',
                'label' => 'Only Cudi',
                'value' => true,
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'send_mail',
                'label' => 'Send Mail',
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'test',
                'label' => 'Test',
                'value' => true,
            )
        );

        $this->addSubmit('Assign', 'action');
    }
}
