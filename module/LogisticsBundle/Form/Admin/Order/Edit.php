<?php

namespace LogisticsBundle\Form\Admin\Order;

use LogisticsBundle\Entity\Order;

/**
 * Edit Order
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\Order\Add
{
    /**
     * @var Order
     */
    private $order;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes', 'order_edit');

        if ($this->order !== null) {
            $this->bind($this->order);
        }
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
