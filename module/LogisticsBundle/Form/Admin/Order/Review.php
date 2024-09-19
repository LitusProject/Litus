<?php

namespace LogisticsBundle\Form\Admin\Order;

use LogisticsBundle\Entity\Order;

/**
 * Add Order
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Review extends \LogisticsBundle\Form\Admin\Order\Add
{
    /**
     * @var Order
     */
    private $order;

    protected $hydrator = 'LogisticsBundle\Hydrator\Order';

    public function init()
    {
        parent::init();

        $this->remove('submit')->addSubmit('orderReview', 'orderSubmit hide');

        if ($this->order !== null) {
            $this->bind($this->order);

            if (!$this->order->getUnits()) {
                $this->remove('unit');
            }
        }
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
