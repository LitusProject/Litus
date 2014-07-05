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

namespace CommonBundle\Controller\Admin;

use Zend\View\Model\ViewModel;

/**
 * CacheController
 *
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class CacheController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (getenv('APPLICATION_ENV') != 'development') {
            $paginator = $this->paginator()->createFromArray(
                $this->getCache()->getOptions()->getResourceManager()->getResource($this->getCache()->getOptions()->getResourceId())->getAllKeys(),
                $this->getParam('page')
            );
        } else {
            $paginator = $this->paginator()->createFromArray(
                array(),
                $this->getParam('page')
            );
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function flushAction()
    {
        $this->getCache()->flush();

        $this->flashMessenger()->success(
            'Success',
            'The cache was successfully cleared!'
        );

        $this->redirect()->toRoute(
            'common_admin_cache',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }
}
