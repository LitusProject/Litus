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

namespace ShopBundle\Controller;

use Doctrine\DBAL\Schema\View,
    Zend\View\Model\ViewModel;

/**
 * ShopController
 *
 * @author Floris Kint <floris.kint@litus.cc>
 */
class ShopController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        //TODO
        $canReserve = true;

        return new ViewModel(
            array(
                "canReserve" => $canReserve,
            )
        );
    }

    public function reserveAction()
    {
        $canReserve = true;

        return new ViewModel(
            array(
                "canReserve" => $canReserve,
            )
        );
    }

    public function reservationsAction()
    {
        //TODO
        $canReserve = true;

        return new ViewModel(
            array(
                "canReserve" => $canReserve,
            )
        );
    }
}
