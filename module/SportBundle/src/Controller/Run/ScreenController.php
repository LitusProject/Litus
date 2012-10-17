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

namespace SportBundle\Controller\Run;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    DateInterval,
    DateTime,
    SportBundle\Entity\Lap,
    SportBundle\Entity\Runner,
    SportBundle\Form\Queue\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * ScreenController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class ScreenController extends \SportBundle\Component\Controller\RunController
{
    public function indexAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
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
        $address = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_remote_host');
        $port = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('sport.queue_socket_port');

        return 'ws://' . $address . ':' . $port;
    }
}
