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

namespace ShopBundle\Form\Admin\SalesSession;

use ShopBundle\Entity\SalesSession;

/**
 * Edit SalesSession
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class Edit extends Add
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
                ->getRepository('ShopBundle\Entity\Product\SessionStockEntry')
                ->getProductAvailability($product, $this->salesSession);

            $this->add(array(
                'type'    => 'number',
                'name'    => $product->getId() . '-quantity',
                'options' => array(
                    'label' => $product->getName(),
                ),
                'attributes' => array(
                    'min'   => '0',
                    'max'   => '100',
                    'value' => $currentAvailability,
                ),
            ));
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
