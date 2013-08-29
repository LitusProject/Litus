<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CommonBundle\Controller;

use Zend\View\Model\ViewModel;

/**
 * RobotsController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class RobotsController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function indexAction()
    {
        $robots = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('common.robots');

        return new ViewModel(
            array(
                'robots' => $robots,
            )
        );
    }
}