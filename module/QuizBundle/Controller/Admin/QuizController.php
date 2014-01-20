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

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Quiz,
    QuizBundle\Form\Admin\Quiz\Add as AddForm,
    QuizBundle\Form\Admin\Quiz\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * QuizController
 *
 * Controller for /admin/quiz[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class QuizController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $quizes = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Quiz')
            ->findAll();

        foreach ($quizes as $key => $quiz) {
            if (!$quiz->canBeEditedBy($this->getAuthentication()->getPersonObject()))
                unset($quizes[$key]);
        }

        $paginator = $this->paginator()->createFromArray(
            $quizes, $this->getParam('page')
        );

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

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $editRoles = array();
                if (isset($formData['edit_roles'])) {
                    foreach ($formData['edit_roles'] as $editRole) {
                        $editRoles[] = $this->getEntityManager()
                            ->getRepository('CommonBundle\Entity\Acl\Role')
                            ->findOneByName($editRole);
                    }
                }

                $quiz = new Quiz($this->getAuthentication()->getPersonObject(), $formData['name'], $editRoles);
                $this->getEntityManager()->persist($quiz);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The quiz was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'add',
                        'quizid' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($quiz = $this->_getQuiz()))
            return new ViewModel();

        $form  = new EditForm($this->getEntityManager(), $quiz);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $quiz->setName($formData['name']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The quiz was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_quiz',
                    array(
                        'action' => 'manage',
                    )
                );
            }
        }

        return new ViewModel(
            array(
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($quiz = $this->_getQuiz()))
            return new ViewModel();

        $this->getEntityManager()->remove($quiz);

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success'
                ),
            )
        );
    }

    /**
     * @return null|\QuizBundle\Entity\Quiz
     */
    private function _getQuiz()
    {
        if ($this->getParam('id') === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the quiz!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        $quiz = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Quiz')
            ->findOneById($this->getParam('id'));

        if ($quiz === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No quiz with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        if (!$quiz->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'You do not have the permissions to modify this quiz!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage'
                )
            );

            return;
        }

        return $quiz;
    }
}
