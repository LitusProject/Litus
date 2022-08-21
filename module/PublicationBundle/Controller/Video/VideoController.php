<?php

namespace PublicationBundle\Controller\Video;

use Laminas\View\Model\ViewModel;

/**
 * VideoController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class VideoController extends \CommonBundle\Component\Controller\ActionController\SiteController
{

    public function viewAction()
    {
        $videos = $this->getEntityManager()
            ->getRepository('PublicationBundle\Entity\Video')
            ->findAllQuery()
            ->getResult();

        return new ViewModel(
            array(
                'videos'      => $videos,
            )
        );
    }
}
