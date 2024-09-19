<?php

namespace QuizBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Team;

/**
 * TeamController
 *
 * Controller for /admin/quiz/:quizid/team[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class TeamController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'QuizBundle\Entity\Team',
            $this->getParam('page'),
            array(
                'quiz' => $quiz,
            ),
            array(
                'number' => 'ASC',
            )
        );

        return new ViewModel(
            array(
                'quiz'              => $quiz,
                'paginator'         => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $form = $this->getForm('quiz_team_add', array('quiz' => $quiz));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $team = $form->hydrateObject(
                    new Team($quiz)
                );

                $this->getEntityManager()->persist($team);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The team was successfully added!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_team',
                    array(
                        'action' => 'manage',
                        'quizid' => $quiz->getId(),
                    )
                );
            }
        }

        $nextTeamNumber = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Team')
            ->getNextTeamNumberForQuiz($quiz);

        $form->get('number')->setValue($nextTeamNumber);

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'form' => $form,
            )
        );
    }

    public function editAction()
    {
        $team = $this->getTeamEntity();
        if ($team === null) {
            return new ViewModel();
        }

        $form = $this->getForm('quiz_team_edit', array('team' => $team));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The team was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_team',
                    array(
                        'action' => 'manage',
                        'quizid' => $team->getQuiz()->getId(),
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

        $team = $this->getTeamEntity();
        if ($team === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($team);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    /**
     * @return Quiz|null
     */
    private function getQuizEntity()
    {
        $quiz = $this->getEntityById('QuizBundle\Entity\Quiz', 'quizid');

        if (!($quiz instanceof Quiz) || !$quiz->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No quiz was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $quiz;
    }

    /**
     * @return Team|null
     */
    private function getTeamEntity()
    {
        $team = $this->getEntityById('QuizBundle\Entity\Team');

        if (!($team instanceof Team) || !$team->getQuiz()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
            $this->flashMessenger()->error(
                'Error',
                'No team was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $team;
    }
}
