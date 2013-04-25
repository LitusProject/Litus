<?php

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Quiz,
    QuizBundle\Entity\Round,
    QuizBundle\Form\Admin\Quiz\Add as AddForm,
    QuizBundle\Form\Admin\Quiz\Edit as EditForm,
    QuizBundle\Form\Admin\Round\Add as AddRoundForm,
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
                        'action' => 'addRounds',
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

    public function addRoundsAction()
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
}
