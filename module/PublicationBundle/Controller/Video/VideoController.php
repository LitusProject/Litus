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
            ->findAllByDate()
            ->getResult();

        foreach ($videos as $video) {
            $video->setUrl($video->getEmbedUrl());
        }

        return new ViewModel(
            array(
                'videos' => $videos,
            )
        );
    }
}
