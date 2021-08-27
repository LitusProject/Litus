<?php

namespace ShopBundle\Form\Shop;

/**
 * Reserve
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Reserve extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Reservation';

    private $stockEntries = array();
    private $salesSession;

    public function init()
    {
        parent::init();
        $this->addClass('form-horizontal');

        foreach ($this->stockEntries as $stockEntry) {
            $product = $stockEntry->getProduct();

            $availability = max(
                0,
                $this->getEntityManager()
                    ->getRepository('ShopBundle\Entity\Session\Stock')
                    ->getRealAvailability($product, $this->salesSession)
            );

            $this->add(
                array(
                    'type'       => 'number',
                    'name'       => 'product-' . $product->getId(),
                    'label'      => $product->getName($this->getLanguage()->getAbbrev()) . ' (&euro; ' . sprintf('%0.2f', $product->getSellPrice()) . ')',
                    'attributes' => array(
                        'value' => '0',
                        'min'   => '0',
                        'max'   => $availability,
                        'class' => 'product-amount form-control',
                    ),
                )
            );
        }

        $this->addSubmit('Reserve', 'submit');
    }

    /**
     * @param array $stockEntries
     */
    public function setStockEntries($stockEntries)
    {
        $this->stockEntries = $stockEntries;
    }

    /**
     * @param \ShopBundle\Entity\Session $salesSession
     */
    public function setSalesSession($salesSession)
    {
        $this->salesSession = $salesSession;
    }
}
