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

namespace CudiBundle\Controller\Prof\Subject;

use SyllabusBundle\Entity\Subject\Comment,
    SyllabusBundle\Entity\Subject\Reply,
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
        if (!($subject = $this->_getSubject())) {
            return new ViewModel();
        }

        $comments = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\Comment')
            ->findBySubject($subject);

        $commentForm = $this->getForm('cudi_prof_comment_add');
        $replyForm = $this->getForm('cudi_prof_comment_reply');

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($formData['reply']) {
                $replyForm->setData($formData);

                if ($replyForm->isValid()) {
                    $formData = $replyForm->getData();

                    $comment = $this->getEntityManager()
                        ->getRepository('SyllabusBundle\Entity\Subject\Comment')
                        ->findOneById($formData['comment']);

                    $comment->setReadBy(null);

                    $reply = new Reply(
                        $this->getAuthentication()->getPersonObject(),
                        $comment,
                        $formData['reply']
                    );

                    $this->getEntityManager()->persist($reply);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The reply was successfully created!'
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

                if ($commentForm->isValid()) {
                    $formData = $commentForm->getData();

                    $comment = new Comment(
                        $this->getAuthentication()->getPersonObject(),
                        $subject,
                        $formData['text'],
                        'external'
                    );

                    $this->getEntityManager()->persist($comment);
                    $this->getEntityManager()->flush();

                    $this->flashMessenger()->success(
                        'SUCCESS',
                        'The comment was successfully created!'
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

        if (!($comment = $this->_getComment())) {
            return new ViewModel();
        }

        if ($comment->getPerson()->getId() != $this->getAuthentication()->getPersonObject()->getId()) {
            return array(
                'result' => (object) array('status' => 'error'),
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

    /**
     * @return \SyllabusBundle\Entity\Subject|null
     */
    private function _getSubject($id = null)
    {
        $id = $id == null ? $this->getParam('id') : $id;

        if (!($academicYear = $this->getCurrentAcademicYear())) {
            return;
        }

        if (null === $id) {
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the subject!'
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
            $this->flashMessenger()->error(
                'Error',
                'No subject with the given ID was found!'
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
            $this->flashMessenger()->error(
                'Error',
                'No ID was given to identify the comment!'
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
            $this->flashMessenger()->error(
                'Error',
                'No comment with the given ID was found!'
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
