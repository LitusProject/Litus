<?php

namespace CudiBundle\Form\Admin\Stock\Order;

use CudiBundle\Entity\Stock\Order;
use LogicException;

/**
 * Add Order Comment
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Comment extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Order|null
     */
    private $order;

    public function init()
    {
        if ($this->order === null) {
            throw new LogicException('Cannot comment on a null order.');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'textarea',
                'name'       => 'comment',
                'label'      => 'Comment',
                'required'   => true,
                'value'      => $this->order->getComment(),
                'attributes' => array(
                    'style' => 'height: 50px;',
                ),
                'options' => array(
                    'input' => array(
                        'required' => false,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save', 'edit', 'save');
    }

    /**
     * @param  Order $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
