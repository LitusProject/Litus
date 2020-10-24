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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Controller\Admin\Subject;

use CommonBundle\Component\Util\AcademicYear;
use CommonBundle\Entity\General\AcademicYear as AcademicYearEntity;
use SyllabusBundle\Entity\Subject;
use SyllabusBundle\Entity\Subject\Comment;
use SyllabusBundle\Entity\Subject\Reply;
use Laminas\View\Model\ViewModel;

/**
 * CommentController
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class CommentController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $academicYear = $this->getAcademicYearEntity();
        if ($academicYear === null) {
            return new ViewModel();
        }

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
                'academicYears'       => $academicYears,
                'currentAcademicYear' => $academicYear,
                'paginator'           => $paginator,
                'paginationControl'   => $this->paginator()->createControl(true),
            )
        );
    }

    public function subjectAction()
    {
        $subject = $this->getSubjectEntity();
        if ($subject === null) {
            return new ViewModel();
        }

        $comments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findBySubject($subject);

        $form = $this->getForm('syllabus_subject_comment_add');

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $comment = $form->hydrateObject(
                    new Comment($this->getAuthentication()->getPersonObject(), $subject)
                );

                $this->getEntityManager()->persist($comment);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'SUCCESS',
                    'The comment was successfully created!'
                );

                $this->redirect()->toRoute(
                    'syllabus_admin_subject_comment',
                    array(
                        'action' => 'subject',
                        'id'     => $subject->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'subject'  => $subject,
                'form'     => $form,
                'comments' => $comments,
            )
        );
    }

    public function replyAction()
    {
        $comment = $this->getCommentEntity();
        if ($comment === null) {
            return new ViewModel();
        }

        $replies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Reply')
            ->findAllByComment($comment);

        $form = $this->getForm('syllabus_subject_reply_add');

        $markAsReadForm = $this->getForm('syllabus_subject_comment_mark-as-read', array('comment' => $comment));

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if (isset($formData['mark_as_read'])) {
                $markAsReadForm->setData($formData);

                if ($markAsReadForm->isValid()) {
                    if ($comment->isRead()) {
                        $comment->setReadBy(null);
                    } else {
                        $comment->setReadBy($this->getAuthentication()->getPersonObject());
                    }

                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The comment status was successfully updated!'
                    );

                    $this->redirect()->toRoute(
                        'syllabus_admin_subject_comment',
                        array(
                            'action' => 'reply',
                            'id'     => $comment->getId(),
                        )
                    );

                    return new ViewModel();
                }
            } else {
                $form->setData($formData);

                if ($form->isValid()) {
                    $reply = $form->hydrateObject(
                        new Reply($this->getAuthentication()->getPersonObject(), $comment)
                    );

                    $this->getEntityManager()->persist($reply);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The reply was successfully created!'
                    );

                    $this->redirect()->toRoute(
                        'syllabus_admin_subject_comment',
                        array(
                            'action' => 'reply',
                            'id'     => $comment->getId(),
                        )
                    );

                    return new ViewModel();
                }
            }
        }

        return new ViewModel(
            array(
                'comment'        => $comment,
                'replies'        => $replies,
                'form'           => $form,
                'markAsReadForm' => $markAsReadForm,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        $comment = $this->getCommentEntity();
        if ($comment === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($comment);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => (object) array('status' => 'success'),
            )
        );
    }

    /**
     * @return Subject|null
     */
    private function getSubjectEntity()
    {
        $subject = $this->getEntityById('SyllabusBundle\Entity\Subject');

        if (!($subject instanceof Subject)) {
            $this->flashMessenger()->error(
                'Error',
                'No subject was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $subject;
    }

    /**
     * @return Comment|null
     */
    private function getCommentEntity()
    {
        $comment = $this->getEntityById('SyllabusBundle\Entity\Subject\Comment');

        if (!($comment instanceof Comment)) {
            $this->flashMessenger()->error(
                'Error',
                'No comment was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_study',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $comment;
    }

    /**
     * @return AcademicYearEntity|null
     */
    protected function getAcademicYearEntity()
    {
        $date = null;
        if ($this->getParam('academicyear') !== null) {
            $date = AcademicYear::getDateTime($this->getParam('academicyear'));
        }
        $academicYear = AcademicYear::getOrganizationYear($this->getEntityManager(), $date);

        if (!($academicYear instanceof AcademicYearEntity)) {
            $this->flashMessenger()->error(
                'Error',
                'No academic year was found!'
            );

            $this->redirect()->toRoute(
                'syllabus_admin_subject_comment',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $academicYear;
    }
}
