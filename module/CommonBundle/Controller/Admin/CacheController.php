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

namespace CommonBundle\Controller\Admin;

use Zend\Cache\Storage\Adapter\MemcachedOptions,
    Zend\Cache\Storage\FlushableInterface,
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
        $options = $this->getCache()->getOptions();
        $keys = array();

        if ($options instanceof MemcachedOptions) {
            $keys = $options->getResourceManager()->getResource($options->getResourceId())->getAllKeys();
        }

        $paginator = $this->paginator()->createFromArray(
            $keys,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function flushAction()
    {
        $cache = $this->getCache();
        if ($cache instanceof FlushableInterface) {
            $cache->flush();

            $this->flashMessenger()->success(
                'Success',
                'The cache was successfully cleared!'
            );
        } else {
            $this->flashMessenger()->success(
                'Success',
                'Failed to clear the cache!'
            );
        }

        $this->redirect()->toRoute(
            'common_admin_cache',
            array(
                'action' => 'manage',
            )
        );

        return new ViewModel();
    }
}
