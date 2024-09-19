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

    /**
     * @var string
     */
    private $sortby;

    public function init()
    {
        parent::init();

        if ($this->order !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->order));
        }
        if ($this->sortby == 'alpha') {
            $orderItems = $this->getEntityManager()->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByOrderOnAlpha($this->order);
        } else {
            $orderItems = $this->getEntityManager()->getRepository('CudiBundle\Entity\Stock\Order\Item')
                ->findAllByOrderOnBarcode($this->order);
        }

        $items = array();
        foreach ($orderItems as $item) {
            $items[] = array(
                'type'     => 'text',
                'name'     => $item->getArticle()->getId(),
                'label'    => $item->getArticle()->getMainArticle()->getTitle(),
                'required' => false,
                'value'    => $item->getNumber(),
                'options'  => array(
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
                'type'     => 'fieldset',
                'name'     => 'articles',
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

    public function setSortBy(string $sortby)
    {
        $this->sortby = $sortby;

        return $this;
    }
}
