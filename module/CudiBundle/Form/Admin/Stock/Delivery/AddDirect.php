<?php

namespace CudiBundle\Form\Admin\Stock\Delivery;

/**
 * Add Delivery Directly
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddDirect extends \CudiBundle\Form\Admin\Stock\Delivery\Add
{
    public function init()
    {
        parent::init();

        $this->remove('article');

        $this->remove('add')
            ->addSubmit('Add', 'stock_add', 'add_delivery', array('id' => 'add_delivery'));
    }
}
