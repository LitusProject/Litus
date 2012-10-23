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
    SportBundle\Form\Admin\Runner\Edit as EditForm,
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
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
            )
        );
    }

    public function updateAction()
    {
        return new ViewModel(
            array(
                'socketUrl' => $this->getSocketUrl(),
            )
        );
    }

    public function identificationAction()
    {
        $runners = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findAllWithoutIdentification();

        return new ViewModel(
            array(
                'runners' => $runners,
            )
        );
    }

    public function editAction()
    {
        if (!($runner = $this->_getRunner()))
            return new ViewModel();

        $form = new EditForm();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $runner->setRunnerIdentification($formData['runner_identification']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Succes',
                        'The runner was successfully updated!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_run',
                    array(
                        'action' => 'identification'
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'runner' => $runner,
                'form' => $form,
            )
        );
    }

    public function groupsAction()
    {
        $paginator = $this->paginator()->createFromEntity(
            'SportBundle\Entity\Group',
            $this->getParam('page'),
            array(),
            array(
                'name' => 'ASC'
            )
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function _getRunner()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the runner!'
                )
            );

            $this->redirect()->toRoute(
                'admin_run',
                array(
                    'action' => 'identification'
                )
            );

            return;
        }

        $runner = $this->getEntityManager()
            ->getRepository('SportBundle\Entity\Runner')
            ->findOneById($this->getParam('id'));

        if (null === $runner) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No runner with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_run',
                array(
                    'action' => 'identification'
                )
            );

            return;
        }

        return $runner;
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
