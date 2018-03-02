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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShopBundle\Form\Shop;

use Zend\Form\Element;

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
            $availability = max(0, $this->getEntityManager()
                ->getRepository('ShopBundle\Entity\Product\SessionStockEntry')
                ->getRealAvailability($product, $this->salesSession));
            $this->add(array(
                'type'       => 'number',
                'name'       => 'product-' . $product->getId(),
                'label'      => $product->getName() . ' (&euro; ' . sprintf('%0.2f', $product->getSellPrice()) . ')',
                'attributes' => array(
                    'value' => '0',
                    'min'   => '0',
                    'max'   => $availability,
                    'class' => 'product-amount form-control',
                ),
            ));
        }

        $this->addSubmit('Reserve', 'submit');
    }

    /**
     * @param SessionStockEntry[] $stockEntries
     */
    public function setStockEntries($stockEntries)
    {
        $this->stockEntries = $stockEntries;
    }

    /**
     * @param SalesSession $salesSession
     */
    public function setSalesSession($salesSession)
    {
        $this->salesSession = $salesSession;
    }
}
