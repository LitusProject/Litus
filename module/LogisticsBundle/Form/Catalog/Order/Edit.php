<?php

namespace LogisticsBundle\Form\Catalog\Order;

use LogisticsBundle\Entity\Order;

/**
 * The form used to edit an existing Order.
 *
 * @author Robin Wroblowski
 */
class Edit extends \LogisticsBundle\Form\Catalog\Order\Add
{
    /**
     * @var Order
     */
    private $order;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes');

        if ($this->order !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->order));
        }
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }
}
