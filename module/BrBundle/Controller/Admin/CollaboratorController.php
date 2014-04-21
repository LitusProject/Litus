<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace BrBundle\Controller\Admin;

use BrBundle\Form\Admin\Collaborator\Add as AddForm,
    BrBundle\Form\Admin\Collaborator\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * CollaboratorController
 *
 * @author Koen Certyn <koen.certyn@litus.cc>
 */
class CollaboratorController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (null === $this->getParam('field')) {
            $paginator = $this->paginator()->createFromEntity(
                'BrBundle\Entity\Collaborator',
                $this->getParam('page')
            );
        }

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        $form = new AddForm($this->getEntityManager());

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $form = new EditForm($this->getEntityManager(), $this->_getCollaborator());

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {

    }

    private function _getCollaborator()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the collaborator!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_collaborator',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $collaborator = $this->getEntityManager()
            ->getRepository('BrBundle\Entity\Collaborator')
            ->findOneById($this->getParam('id'));

        if (null === $collaborator) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No collaborator with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'br_admin_collaborator',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $collaborator;
    }
}
