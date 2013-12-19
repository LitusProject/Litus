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
    SyllabusBundle\Entity\Subject\Reply,
    SyllabusBundle\Form\Admin\Subject\Comment\Add as AddCommentForm,
    SyllabusBundle\Form\Admin\Subject\Comment\MarkAsRead as MarkAsReadForm,
    SyllabusBundle\Form\Admin\Subject\Reply\Add as AddReplyForm,
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
        if (!($academicYear = $this->_getAcademicYear()))
            return new ViewModel();

        $paginator = $this->paginator()->createFromQuery(
            $this->getEntityManager()
                ->getRepository('SyllabusBundle\Entity\Subject\Comment')
                ->findAllByAcademicYearQuery($academicYear),
            $this->getParam('page')
        );

        $academicYears = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findAll();

        return new ViewModel(
            array(
                'academicYears' => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function subjectAction()
    {
        if (!($subject = $this->_getSubject()))
            return new ViewModel();

        $comments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findBySubject($subject);

        $form = new AddCommentForm();

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if($form->isValid()) {
                $formData = $form->getFormData($formData);

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
                    'syllabus_admin_subject_comment',
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

    public function replyAction()
    {
        if (!($comment = $this->_getComment()))
            return new ViewModel();

        $replies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Reply')
            ->findAllByComment($comment);

        $form = new AddReplyForm();
        $markAsReadForm = new MarkAsReadForm($comment);

        if($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if (isset($formData['mark_as_read'])) {
                $markAsReadForm->setData($formData);

                if($markAsReadForm->isValid()) {
                    $formData = $markAsReadForm->getFormData($formData);

                    if ($comment->isRead())
                        $comment->setReadBy(null);
                    else
                        $comment->setReadBy($this->getAuthentication()->getPersonObject());

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->addMessage(
                        new FlashMessage(
                            FlashMessage::SUCCESS,
                            'SUCCESS',
                            'The comment status was successfully updated!'
                        )
                    );

                    $this->redirect()->toRoute(
                        'syllabus_admin_subject_comment',
                        array(
                            'action' => 'reply',
                            'id' => $comment->getId(),
                        )
                    );

                    return new ViewModel();
                }
            } else {
                $form->setData($formData);

                if($form->isValid()) {
                    $formData = $form->getFormData($formData);

                    $reply = new Reply(
                        $this->getEntityManager(),
                        $this->getAuthentication()->getPersonObject(),
                        $comment,
                        $formData['text']
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
                        'syllabus_admin_subject_comment',
                        array(
                            'action' => 'reply',
                            'id' => $comment->getId(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'comment' => $comment,
                'replies' => $replies,
                'form' => $form,
                'markAsReadForm' => $markAsReadForm,
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
                'syllabus_admin_study',
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
                'syllabus_admin_study',
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
                'syllabus_admin_study',
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
                'syllabus_admin_study',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $comment;
    }

    private function _getAcademicYear()
    {
        $date = null;
        if (null !== $this->getParam('academicyear'))
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (null === $academicYear) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No academic year was found!'
                )
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject_comment',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $academicYear;
    }
}
