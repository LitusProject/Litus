<?php

namespace CudiBundle\Form\Admin\Stock\Delivery;

/**
 * Return to supplier (inverse of delivery)
 *
 * (named so because php complains when 'Return' is used)
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Retour extends \CudiBundle\Form\Admin\Stock\Delivery\Add
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'comment',
                'label'    => 'Comment',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
    }
}
