<?php

namespace CommonBundle\Controller;

use Laminas\View\Model\ViewModel;

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
