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

namespace SyllabusBundle\Controller\Admin\Subject;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    CommonBundle\Component\Util\AcademicYear,
    SyllabusBundle\Entity\Subject\Comment,
    SyllabusBundle\Form\Admin\Subject\Comment\Add as AddForm,
    Zend\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ActionController
{
    public function manageAction()
    {
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        $comments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findBySubject($subject);

        $form = new AddForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $comment = new Comment(
                    $this->getEntityManager(),
                    $this->getAuthentication()->getPersonObject(),
                    $subject,
                    $formData['text'],
                    $formData['type']
                );

                $this->getEntityManager()->persist($comment);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'SUCCESS',
                        'The comment was successfully created!'
                    )
                );

                $this->redirect()->toRoute(
                    'admin_subject_comment',
                    array(
                        'action' => 'manage',
                        'id' => $subject->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subject' => $subject,
                'form' => $form,
                'comments' => $comments,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($comment = $this->_getComment()))
            return new ViewModel();

        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array("status" => "success"),
            )
        );
    }

    private function _getSubject($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (null === $id) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the subject!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $subject = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject')
            ->findOneById($id);

        if (null === $subject) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No subject with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $subject;
    }

    private function _getComment()
    {
        if (null === $this->getParam('id')) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the comment!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        $comment = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findOneById($this->getParam('id'));

        if (null === $comment) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No comment with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $comment;
    }
}
