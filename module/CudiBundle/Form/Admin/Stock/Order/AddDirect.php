<?php

namespace CudiBundle\Form\Admin\Stock\Order;

/**
 * Add Order Directly
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class AddDirect extends \CudiBundle\Form\Admin\Stock\Order\Add
{
    public function init()
    {
        parent::init();

        $this->remove('article');

        $this->remove('add')
            ->addSubmit(
                'Add',
                'stock_add',
                'add_order',
                array(
                    'data-help' => '<p>The article will be added to the order queue. This way a group of articles can be ordered for the same supplier.<p>
                    <p>To finish the order, you have to \'place\' it, this can be done by editing the order.</p>',
                    'id'        => 'stock_add',
                )
            );
    }
}
