<?php

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Quiz,
    QuizBundle\Entity\Round,
    QuizBundle\Form\Admin\Quiz\Add as AddForm,
    QuizBundle\Form\Admin\Quiz\Edit as EditForm,
    QuizBundle\Form\Admin\Round\Add as AddRoundForm,
    QuizBundle\Form\Admin\Round\Edit as EditRoundForm,
    Zend\View\Model\ViewModel;

/**
 * QuizController
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class QuizController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Quiz')
                ->findAll(),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
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

                // XXX: Edit role: In form or edit later?
                $quiz = new Quiz($this->getAuthentication()->getPersonObject(), $formData['name'], array());
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
                    'quiz_admin_quiz',
                    array(
                        'action' => 'addRound',
                        'id' => $quiz->getId(),
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
        if(!($quiz = $this->_getQuiz()))
            return new ViewModel;

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
            return new ViewModel;

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

    public function roundsAction()
    {
        if(!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Round')
                ->findAllFromQuiz($quiz),
            $this->getParam('page')
        );

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addRoundAction()
    {
        if(!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $form = new AddRoundForm($this->getEntityManager());
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
                    'quiz_admin_quiz',
                    array(
                        'action' => 'rounds',
                        'id' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'form' => $form,
            )
        );
    }

    public function editRoundAction()
    {
        if(!($round = $this->_getRound()))
            return new ViewModel;

        $form  = new EditRoundForm($this->getEntityManager(), $round);

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
                    'quiz_admin_quiz',
                    array(
                        'action' => 'rounds',
                        'id' => $round->getQuiz()->getId()
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

    /**
     * @return null|\QuizBundle\Entity\Quiz
     */
    private function _getQuiz()
    {
        if($this->getParam('id') === null) {
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

        if($quiz === null) {
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

        return $quiz;
    }

    /**
     * @return null|\QuizBundle\Entity\Round
     */
    private function _getRound()
    {
        if($this->getParam('id') === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the round!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'rounds',
                )
            );

            return;
        }

        $round = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Round')
            ->findOneById($this->getParam('id'));

        if($round === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No round with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'rounds'
                )
            );

            return;
        }

        return $round;
    }
}
