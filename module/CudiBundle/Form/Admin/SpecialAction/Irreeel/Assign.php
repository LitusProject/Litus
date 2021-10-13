<?php

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
                'options'    => array(
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
