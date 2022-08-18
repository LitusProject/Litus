<?php

namespace PublicationBundle\Controller\Video;

use CommonBundle\Entity\General\AcademicYear;
use Laminas\View\Model\ViewModel;
use PublicationBundle\Entity\Publication;

/**
 * VideoController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class VideoController extends \CommonBundle\Component\Controller\ActionController\SiteController
{

    public function viewAction()
    {
        $video = $this->getVideoEntity();
        if ($video === null) {
            return new ViewModel();
        }

        return new ViewModel(
            array(
                'video'  => $video,
            )
        );
    }

    /**
     * @return Video|null
     */
    private function getVideoEntity()
    {
        $video = $this->getEntityById('PublicationBundle\Entity\Video');

        if (!($video instanceof Video)) {
            $this->flashMessenger()->error(
                'Error',
                'No video was found!'
            );

            $this->redirect()->toRoute(
                'publication_archive',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $video;
    }
}
