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

use CommonBundle\Component\Util\Bytes as BytesUtil;
use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\Cache\Storage\AvailableSpaceCapableInterface;
use Laminas\Cache\Storage\FlushableInterface;
use Laminas\Cache\Storage\TotalSpaceCapableInterface;
use Laminas\View\Model\ViewModel;

/**
 * CacheController
 *
 * @autor Pieter Maene <pieter.maene@litus.cc>
 * @autor Kristof Mariën <kristof.marien@litus.cc>
 */
class CacheController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $info = array();
        $keys = array();

        if ($this->getCache() instanceof AvailableSpaceCapableInterface) {
            $availableSpace = $this->getCache()->getAvailableSpace();
            $info['Available Space'] = BytesUtil::format($availableSpace);
        }

        if ($this->getCache() instanceof TotalSpaceCapableInterface) {
            $totalSpace = $this->getCache()->getTotalSpace();
            $info['Total Space'] = BytesUtil::format($totalSpace);
        }

        if ($this->getCache() instanceof Redis) {
            $redisInfo = $this->getRedisClient()->info();

            $info = array_merge(
                $info,
                array(
                    'Keyspace Hits' => $redisInfo['keyspace_hits'],
                    'Keyspace Misses' => $redisInfo['keyspace_misses'],
                    'Total Connections' => $redisInfo['total_connections_received'],
                    'Total Commands' => $redisInfo['total_commands_processed'],
                )
            );

            $keys = $this->getRedisClient()->keys('cache:*');
        }

        $paginator = $this->paginator()->createFromArray(
            $keys,
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'info'              => $info,
                'flushable'         => $this->getCache() instanceof FlushableInterface,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function flushAction()
    {
        if ($this->getCache() instanceof FlushableInterface) {
            $this->getCache()->flush();

            $this->flashMessenger()->success(
                'Success',
                'The cache was successfully cleared!'
            );
        } else {
            $this->flashMessenger()->error(
                'Error',
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
