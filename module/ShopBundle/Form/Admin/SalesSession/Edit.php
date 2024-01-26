<?php

namespace ShopBundle\Form\Admin\SalesSession;

use ShopBundle\Entity\Session as SalesSession;

/**
 * Edit SalesSession
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Edit extends \ShopBundle\Form\Admin\SalesSession\Add
{
    /**
     * @var SalesSession The session to edit.
     */
    private $salesSession;

    public function init()
    {
        parent::init();

        foreach ($this->products as $product) {
            $this->remove($product->getId() . '-quantity');

            $currentAvailability = $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Session\Stock')
                ->getProductAvailability($product, $this->salesSession);

            $this->add(
                array(
                    'type'       => 'number',
                    'name'       => $product->getId() . '-quantity',
                    'value'      => $currentAvailability,
                    'options'    => array(
                        'label' => $product->getName(),
                    ),
                    'attributes' => array(
                        'min' => '0',
                        'max' => '100',
                    ),
                )
            );
        }
        $this->remove('session_add')
            ->addSubmit('Save', 'edit');

        $this->bind($this->salesSession);
    }

    /**
     * @param  SalesSession $salesSession
     * @return self
     */
    public function setSalesSession(SalesSession $salesSession)
    {
        $this->salesSession = $salesSession;

        return $this;
    }
}
