<?php

namespace CudiBundle\Controller\Supplier;

use Laminas\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class IndexController extends \CudiBundle\Component\Controller\SupplierController
{
    public function indexAction()
    {
        $supplier = $this->getSupplierEntity();

        return new ViewModel(
            array(
                'supplier' => $supplier,
            )
        );
    }
}
