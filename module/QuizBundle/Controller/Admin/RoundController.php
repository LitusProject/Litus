<?php

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Round,
    QuizBundle\Form\Admin\Round\Add as AddForm,
    QuizBundle\Form\Admin\Round\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * RoundController
 *
 * Controller for /admin/quiz/:quizid/round[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class RoundController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Round')
                ->findByQuiz($quiz),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(true),
            )
        );
    }

    public function addAction()
    {
        if (!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $form = new AddForm($this->getEntityManager(), $quiz);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $round = new Round($quiz, $formData['name'], $formData['max_points'], $formData['order']);
                $this->getEntityManager()->persist($round);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The round was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'manage',
                        'quizid' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $next_round_number = $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Round')
                ->getNextRoundOrderForQuiz($quiz);

        $form->get('order')
            ->setValue($next_round_number);

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($round = $this->_getRound()))
            return new ViewModel;

        $form  = new EditForm($this->getEntityManager(), $round);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $round->setName($formData['name']);
                $round->setMaxPoints($formData['max_points']);
                $round->setOrder($formData['order']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The round was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'manage',
                        'quizid' => $round->getQuiz()->getId()
                    )
                );
            }
        }
        return new ViewModel(
            array(
                'quiz' => $round->getQuiz(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($round = $this->_getRound()))
            return new ViewModel;

        $this->getEntityManager()->remove($round);

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
        if ($this->getParam('quizid') === null) {
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
            ->findOneById($this->getParam('quizid'));

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

    /**
     * @return null|\QuizBundle\Entity\Round
     */
    private function _getRound()
    {
        if ($this->getParam('id') === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the round!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_round',
                array(
                    'action' => 'manage',
                    'quizid' => $this->getParam('quizid'),
                )
            );

            return;
        }

        $round = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Round')
            ->findOneById($this->getParam('id'));

        if ($round === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No round with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_round',
                array(
                    'action' => 'manage',
                    'quizid' => $this->getParam('quizid'),
                )
            );

            return;
        }

        if (!$round->getQuiz()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
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

        return $round;
    }
}