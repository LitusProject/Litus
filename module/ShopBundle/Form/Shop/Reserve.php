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

namespace ShopBundle\Form\Shop;

use DateInterval,
    DateTime,
    Zend\Form\Element;

/**
 * Reserve
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Reserve extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'ShopBundle\Hydrator\Reservation';

    public function init()
    {
        parent::init();
        $this->add(array(
            'type' => 'select',
            'name' => 'salesSession',
            'label' => 'Sales Session',
            'required' => true,
            'escape' => false,
            'attributes' => array(
                'options' => $this->createSalesSessionsArray(),
            ),
        ));

        $this->add(array(
            'type' => 'select',
            'name' => 'product',
            'label' => 'Product',
            'required' => true,
            'attributes' => array(
                'options' => $this->createProductsArray(),
                'escape' => false,
            ),
        ));

        $this->add(array(
            'type' => 'select',
            'name' => 'amount',
            'label' => 'Amount',
            'required' => true,
            'attributes' => array(
                'options' => $this->createAmountsArray(),
            ),
            'options' => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Reserve', 'reserve');
    }

    /**
	 * @return array
	 */
    private function createSalesSessionsArray()
    {
        $interval = new DateInterval(
            $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('shop.reservation_treshold')
        );

        $startDate = new DateTime();
        $endDate = clone $startDate;
        $endDate->add($interval);

        $salesSessions = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\SalesSession')
            ->findAllReservationsPossibleInterval($startDate, $endDate);
        $result = array();
        foreach ($salesSessions as $session) {
            $result[$session->getId()] = $session->getStartDate()->format('d/m/Y H:i') . ' - ' . $session->getEndDate()->format('d/m/Y H:i');
        }

        return $result;
    }

    /**
	 * @return array
	 */
    private function createProductsArray()
    {
        $products = $this->getEntityManager()
            ->getRepository('ShopBundle\Entity\Product')
            ->findAllAvailable();
        $result = [];
        foreach ($products as $product) {
            $result[$product->getId()] = $product->getName() . ' (' . html_entity_decode('&euro; ', ENT_COMPAT, 'UTF-8') .  $product->getSellPrice() . ')';
        }

        return $result;
    }

    /**
	 * @return array
	 */
    private function createAmountsArray()
    {
        $result = array();
        for ($i = 1; $i <= 15; ++$i) {
            $result[$i] = $i;
        }

        return $result;
    }
}
