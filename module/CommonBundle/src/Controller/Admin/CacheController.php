<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * CacheController
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class CacheController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        return new ViewModel(
            array(
                'cache' => $this->getCache(),
            )
        );
    }

    public function flushAction()
    {
        $this->getCache()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Success',
                'The cache was successfully cleared!'
            )
        );

        $this->redirect()->toRoute(
            'admin_cache',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }
}
