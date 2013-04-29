<?php

namespace QuizBundle\Controller\Admin;

use CommonBundle\Component\FlashMessenger\FlashMessage,
    QuizBundle\Entity\Quiz,
    QuizBundle\Entity\Round,
    QuizBundle\Entity\Team,
    QuizBundle\Form\Admin\Quiz\Add as AddForm,
    QuizBundle\Form\Admin\Quiz\Edit as EditForm,
    QuizBundle\Form\Admin\Round\Add as AddRoundForm,
    QuizBundle\Form\Admin\Round\Edit as EditRoundForm,
    QuizBundle\Form\Admin\Team\Add as AddTeamForm,
    QuizBundle\Form\Admin\Team\Edit as EditTeamForm,
    QuizBundle\Form\Admin\Point\Add as AddPointForm,
    Zend\View\Model\ViewModel;

/**
 * QuizController
 *
 * Controller for /admin/quiz[/:action[/:id]][/page/:page][/]
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

    public function moderateAction()
    {
        if(!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $rounds = $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Round')
                ->findByQuiz($quiz);
        $teams = $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Team')
                ->findByQuiz($quiz);

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'rounds' => $rounds,
                'teams' => $teams,
            )
        );
    }

    public function pointsAction()
    {

        $this->initAjax();

        if(!($quiz = $this->_getQuiz()))
            return new ViewModel;
        $data = array();
        $points = $this->getEntityManager()
                ->getRepository('QuizBundle\Entity\Point')
                ->findByQuiz($quiz);
        foreach($points as $point) {
            $data[] = array(
                'round' => $point->getRound()->getId(),
                'team' => $point->getTeam()->getId(),
                'value' => $point->getPoint(),
            );
        }

        return new ViewModel(
            array(
                'json' => $data,
            )
        );
    }

    public function addPointAction()
    {
        if(!($quiz = $this->_getQuiz()))
            return new ViewModel;

        $form = new AddPointForm($this->getEntityManager(), $quiz);
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                $formData = $form->getFormData($formData);


                $team = $this->getEntityManager()
                        ->find('QuizBundle\Entity\Team', $formData['team']);
                $round = $this->getEntityManager()
                        ->find('QuizBundle\Entity\Round', $formData['round']);

                $point = $this->getEntityManager()
                        ->getRepository('QuizBundle\Entity\Point')
                        ->findOneBy(
                            array(
                                'team' => $team,
                                'round' => $round,
                            )
                        );

                if($point === null) {
                    $point = new \QuizBundle\Entity\Point($round, $team, 0);
                    $this->getEntityManager()->persist($point);
                }

                $point->setPoint($formData['points']);

                $this->getEntityManager()->flush();

                if($this->_isAjax())
                            return new ViewModel(
                                array(
                                    'json' => array(
                                        'status' => 'success'
                                    ),
                                )
                            );

                $this->flashMessenger()->addMessage(
                    new FlashMessage(
                        FlashMessage::SUCCESS,
                        'Success',
                        'The point was successfully added!'
                    )
                );

                $this->redirect()->toRoute(
                    'quiz_admin_quiz',
                    array(
                        'action' => 'addPoint',
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

    private function _isAjax()
    {
        return ($this->getRequest()->getHeaders()->get('X_REQUESTED_WITH')
            && 'XMLHttpRequest' == $this->getRequest()->getHeaders()->get('X_REQUESTED_WITH')->getFieldValue());
    }
}
