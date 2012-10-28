<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Controller\Admin;

use Zend\View\Model\ViewModel;

/**
 * AllInstallController calls all other installations.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class AllInstallController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction() {
        $bundles = array(
            'api',
            'banner',
            'br',
            'calendar',
            'common',
            'cudi',
            'form',
            'gallery',
            'logistics',
            'mail',
            'news',
            'notification',
            'on',
            'page',
            'publication',
            'secretary',
            'shift',
            'sport',
            'syllabus',
            'wiki',
        );

        return new ViewModel(
            array(
                'bundles' => $bundles,
            )
        );
    }
}
