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

namespace LogisticsBundle\Form\Admin\Order;

use LogisticsBundle\Entity\Order;

/**
 * Edit Order
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \LogisticsBundle\Form\Admin\Order\Add
{
    /**
     * @var Order
     */
    private $order;

    public function init()
    {
        parent::init();

        $this->remove('submit')
            ->addSubmit('Save Changes');

        if ($this->order !== null) {
            $hydrator = $this->getHydrator();
            $this->populateValues($hydrator->extract($this->order));
        }
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
