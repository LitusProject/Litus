<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Stock\Order;

use CudiBundle\Entity\Stock\Order\Item,
    LogicException;

/**
 * Edit Order
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Item|null
     */
    private $item;

    public function init()
    {
        if (null === $this->item) {
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
