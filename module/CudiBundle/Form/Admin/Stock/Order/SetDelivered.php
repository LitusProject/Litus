<?php

namespace CudiBundle\Form\Admin\Stock\Order;

use CudiBundle\Entity\Stock\Order;

/**
 * Set delivered
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class SetDelivered extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var Order
     */
    private $order;

    public function init()
    {
        parent::init();

        if ($this->order !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->order));
        }
        $items = array();
        foreach ($this->order->getItems() as $item) {
            $items[] = array(
                'type' => 'text',
                'name' => $item->getArticle()->getId(),
                'label' => $item->getArticle()->getMainArticle()->getTitle(),
                'required' => false,
                'value' => $item->getNumber(),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'Int',
                            ),
                        ),
                    ),
                ),
            );
        }

        $this->add(
            array(
                'type' => 'fieldset',
                'name' => 'articles',
//                'label' => 'Articles',
                'elements' => $items,
            )
        );

        $this->addSubmit('Set delivered', 'set_delivered');
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
