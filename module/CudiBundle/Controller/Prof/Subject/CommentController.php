<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace CudiBundle\Controller\Prof\Subject;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    SyllabusBundle\Entity\Subject\Comment,
    SyllabusBundle\Entity\Subject\Reply,
    CudiBundle\Form\Prof\Comment\Add as AddCommentForm,
    CudiBundle\Form\Prof\Comment\Reply as AddReplyForm,
    Zend\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CudiBundle\Component\Controller\ProfController
{
    public function manageAction()
    {
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        $comments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findBySubject($subject);

        $commentForm = new AddCommentForm();
        $replyForm = new AddReplyForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($formData['reply']) {
                $replyForm->setData($formData);

                if ($replyForm->isValid()) {
                    $formData = $replyForm->getFormData($formData);

                    $reply = new Reply(
                        $this->getEntityManager(),
                        $this->getAuthentication()->getPersonObject(),
                        $this->getEntityManager()
                            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
                            ->findOneById($formData['comment']),
                        $formData['reply']
                    );

                    $this->getEntityManager()->persist($reply);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The reply was successfully created!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'cudi_prof_subject_comment',
                        array(
                            'action' => 'manage',
                            'id' => $subject->getId(),
                            'language' => $this->getLanguage()->getAbbrev(),
                        )
                    );

                    return new ViewModel();
                }
            } else {
                $commentForm->setData($formData);

                if($commentForm->isValid()) {
                    $formData = $commentForm->getFormData($formData);

                    $comment = new Comment(
                        $this->getEntityManager(),
                        $this->getAuthentication()->getPersonObject(),
                        $subject,
                        $formData['text'],
                        'external'
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
                        'cudi_prof_subject_comment',
                        array(
                            'action' => 'manage',
                            'id' => $subject->getId(),
                            'language' => $this->getLanguage()->getAbbrev(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'subject' => $subject,
                'commentForm' => $commentForm,
                'replyForm' => $replyForm,
                'comments' => $comments,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($comment = $this->_getComment()))
            return new ViewModel();

        if ($comment->getPerson()->getId() != $this->getAuthentication()->getPersonObject()->getId()) {
            return array(
                'result' => (object) array("status" => "error")
            );
        }

        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    private function _getSubject($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (!($academicYear = $this->getAcademicYear()))
            return;

        if (null === $id) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No ID was given to identify the subject!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $mapping = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\SubjectProfMap')
            ->findOneBySubjectIdAndProfAndAcademicYear(
                $id,
                $this->getAuthentication()->getPersonObject(),
                $academicYear
            );

        if (null === $mapping) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No subject with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_prof_subject',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $mapping->getSubject();
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
                'cudi_prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        $comment = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findOneById($this->getParam('id'));

        if (null === $comment || null === $this->_getSubject($comment->getSubject()->getId())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No comment with the given ID was found!'
                )
            );

            $this->redirect()->toRoute(
                'cudi_prof_article',
                array(
                    'action' => 'manage',
                    'language' => $this->getLanguage()->getAbbrev(),
                )
            );

            return;
        }

        return $comment;
    }
}
