<?php

namespace CudiBundle\Form\Sale\Sale;

/**
 * Return Sale
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class ReturnArticle extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'person',
                'label'      => 'Person',
                'required'   => true,
                'attributes' => array(
                    'placeholder' => 'Student',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'typeahead',
                'name'       => 'article',
                'label'      => 'Article',
                'required'   => true,
                'attributes' => array(
                    'placeholder' => 'Article',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadSaleArticle'),
                            array('name' => 'HasBought'),
                        ),
                    ),
                ),
            )
        );

        echo '<div id="recent-items-container" style="display: none;"></div>';

        $this->addSubmit(
            'Return',
            null,
            'submit',
            array(
                'autocomplete' => 'off',
                'id'           => 'signin',
            )
        );

        $this->add(
            array(
                'type'  => 'reset',
                'name'  => 'cancel',
                'value' => 'Cancel',
            )
        );
    }
}
