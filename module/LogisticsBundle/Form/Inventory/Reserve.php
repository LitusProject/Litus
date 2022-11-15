<?php

namespace LogisticsBundle\Form\Inventory;

class Reserve extends \CommonBundle\Component\Form\Bootstrap\Form
{

    public function init()
    {
        parent::init();

        $product = $this->getEntityManager()
            ->getRepository('LogisticsBundle\Entity\Inventory')
            ->findOneById($this->getParam('id'));

        $amount = max(
            0,
            $this->getEntityManager()
                ->getRepository('LogisticsBundle\Entity\Inventory')
                ->getAmount($product)
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'reserve',
                'label'    => 'Reserve',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
                'attributes' => array(
                    'id'           => 'reserve',
                    'placeholder'  => 'Reserve',
                    'value' => '0',
                    'min'   => '0',
                    'max'   => $amount,
                ),
            )
        );

        $this->addSubmit('Reserve', 'inventory_reserve');
    }
}