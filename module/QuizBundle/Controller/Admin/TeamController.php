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

use QuizBundle\Entity\Quiz,
    QuizBundle\Entity\Team,
    Zend\View\Model\ViewModel;

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
        if (!($quiz = $this->getQuizEntity())) {
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
                'quiz' => $quiz,
                'paginator' => $paginator,
                'paginationControl' => $this->paginator()->createControl(),
            )
        );
    }

    public function addAction()
    {
        if (!($quiz = $this->getQuizEntity())) {
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
        if (!($team = $this->getTeamEntity())) {
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

        if (!($team = $this->getTeamEntity())) {
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
