<?php

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Team,
    QuizBundle\Form\Admin\Team\Add as AddForm,
    QuizBundle\Form\Admin\Team\Edit as EditForm,
    Zend\View\Model\ViewModel;

/**
 * TeamController
 *
 * Controller for /admin/quiz/:quizid/team[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <vierbergenlars@gmail.com>
 */
class TeamController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        if (!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $paginator = $this->paginator()->createFromArray(
            $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Team')
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

                $team = new Team($quiz, $formData['name'], $formData['number']);
                $this->getEntityManager()->persist($team);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The team was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_team',
                    array(
                        'action' => 'manage',
                        'quizid' => $quiz->getId(),
                    )
                );

                return new ViewModel();
            }
        }

        $next_team_number = $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Team')
                ->getNextTeamNumberForQuiz($quiz);

        $form->get('number')
            ->setValue($next_team_number);

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        if (!($team = $this->_getTeam()))
            return new ViewModel;

        $form  = new EditForm($this->getEntityManager(), $team);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);

                $team->setName($formData['name']);
                $team->setNumber($formData['number']);

                $this->getEntityManager()->flush();

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The team was successfully edited!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_team',
                    array(
                        'action' => 'manage',
                        'quizid' => $team->getQuiz()->getId()
                    )
                );
            }
        }
        return new ViewModel(
            array(
                'quiz' => $team->getQuiz(),
                'form' => $form,
            )
        );
    }

    public function deleteAction()
    {
        $this->initAjax();

        if (!($team = $this->_getTeam()))
            return new ViewModel;

        $this->getEntityManager()->remove($team);

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

        return $quiz;
    }

    /**
     * @return null|\QuizBundle\Entity\Team
     */
    private function _getTeam()
    {
        if ($this->getParam('id') === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No id was given to identify the team!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_team',
                array(
                    'action' => 'manage',
                    'quizid' => $this->getParam('quizid'),
                )
            );

            return;
        }

        $team = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Team')
            ->findOneById($this->getParam('id'));

        if ($team === null) {
            $this->flashMessenger()->addMessage(
                new FlashMessage(
                    FlashMessage::ERROR,
                    'Error',
                    'No team with the given id was found!'
                )
            );

            $this->redirect()->toRoute(
                'quiz_admin_team',
                array(
                    'action' => 'manage',
                    'quizid' => $this->getParam('quizid'),
                )
            );

            return;
        }

        return $team;
    }
}