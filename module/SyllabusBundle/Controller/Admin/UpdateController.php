<?php

namespace SyllabusBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;

/**
 * UpdateController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class UpdateController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function indexAction()
    {
        $allowUpdate = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.enable_update');

        return new ViewModel(
            array(
                'academicYear' => $this->getCurrentAcademicYear(),
                'allowUpdate'  => $allowUpdate,
                'socketUrl'    => $this->getSocketUrl(),
                'authSession'  => $this->getAuthentication()
                    ->getSessionObject(),
                'key'          => $this->getEntityManager()
                    ->getRepository('CommonBundle\Entity\General\Config')
                    ->getConfigValue('syllabus.update_socket_key'),
            )
        );
    }

    /**
     * Returns the WebSocket URL.
     *
     * @return string
     */
    protected function getSocketUrl()
    {
        return $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('syllabus.update_socket_public');
    }
}
