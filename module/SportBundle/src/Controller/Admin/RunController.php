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

namespace SportBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    Zend\View\Model\ViewModel;

/**
 * RunController
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class RunController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function queueAction()
    {
        $previousLaps = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findPrevious(5);
        $nextLaps = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext(15);

        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
                'currentLap' => $this->_getCurrentLap(),
                'nextLap' => $this->_getNextLap(),
                'previousLaps' => $previousLaps,
                'nextLaps' => $nextLaps,
            )
        );
    }

    public function startAction()
    {
        if (null !== $this->_getCurrentLap())
            $this->_getCurrentLap()->stop();

        if (null !== $this->_getNextLap())
            $this->_getNextLap()->start();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The next lap was successfully started!'
            )
        );

        $this->redirect()->toRoute(
            'admin_lap',
            array(
                'action' => 'queue'
            )
        );

        return new ViewModel();
    }

    public function stopAction()
    {
        if (null !== $this->_getCurrentLap())
            $this->_getCurrentLap()->stop();

        $this->getEntityManager()->flush();

        $this->flashMessenger()->addMessage(
            new FlashMessage(
                FlashMessage::SUCCESS,
                'Succes',
                'The current lap was successfully stopped!'
            )
        );

        $this->redirect()->toRoute(
            'admin_lap',
            array(
                'action' => 'queue'
            )
        );

        return new ViewModel();
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($lap = $this->_getKey()))
            return new ViewModel();

        $this->getEntityManager()->remove($lap);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    private function _getLap()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the lap!'
                )
            );

            $this->redirect()->toRoute(
                'admin_lap',
                array(
                    'action' => 'queue'
                )
            );

            return;
        }

        $lap = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findOneById($this->getParam('id'));

        if (null === $key) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No lap with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_lap',
                array(
                    'action' => 'queue'
                )
            );

            return;
        }

        return $key;
    }

    private function _getCurrentLap()
    {
        return $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findCurrent();
    }

    private function _getNextLap()
    {
        return $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Lap')
            ->findNext();
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
