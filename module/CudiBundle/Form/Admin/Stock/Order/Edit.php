<?php

namespace CudiBundle\Form\Admin\Stock\Order;

use CudiBundle\Entity\Stock\Order\Item;
use LogicException;

/**
 * Edit Order
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \CudiBundle\Form\Admin\Stock\Order\Add
{
    /**
     * @var Item|null
     */
    private $item;

    public function init()
    {
        if ($this->item === null) {
            throw new LogicException('Cannot edit a null order item.');
        }

        parent::init();

        $this->remove('article');

        $this->get('number')
            ->setValue($this->item->getNumber());

        $this->remove('add')
            ->addSubmit('Save', 'stock_edit', 'edit');
    }

    /**
     * @param  Item $item
     * @return self
     */
    public function setItem(Item $item)
    {
        $this->item = $item;

        return $this;
    }
}
