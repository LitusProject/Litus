<?php

namespace LogisticsBundle\Form\Catalog\Order;

use LogisticsBundle\Entity\Order;

/**
 * The form used to edit an existing Order.
 *
 * @author Robin Wroblowski
 * @author Pedro Devogelaere <pedro.devogelaere@vtk.be>
 */
class Edit extends \LogisticsBundle\Form\Catalog\Order\Add
{
    /**
     * @var Order
     */
    private Order $order;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Update');

        $hydrator = $this->getHydrator();
        $this->populateValues($hydrator->extract($this->order));
    }

    /**
     * @param Order $order
     * @return self
     */
    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }
}
