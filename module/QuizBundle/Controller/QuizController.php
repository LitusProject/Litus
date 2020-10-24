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

namespace QuizBundle\Controller;

use CommonBundle\Entity\User\Person;
use QuizBundle\Entity\Point;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Round;
use QuizBundle\Entity\Team;
use Laminas\View\Model\ViewModel;

/**
 * QuizController
 *
 * Controller for /quiz/:quizid[/:action[/:roundid/:teamid]]
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class QuizController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $rounds = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Round')
            ->findAllByQuiz($quiz);

        $teams = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Team')
            ->findAllByQuiz($quiz);

        $allPoints = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Point')
            ->findAllByQuiz($quiz);

        $points = array();
        foreach ($allPoints as $point) {
            $points[$point->getTeam()->getId()][$point->getRound()->getId()] = $point->getPoint();
        }

        return new ViewModel(
            array(
                'quiz'   => $quiz,
                'rounds' => $rounds,
                'teams'  => $teams,
                'points' => $points,
            )
        );
    }

    public function updateAction()
    {
        $this->initAjax();

        $team = $this->getTeamEntity();
        if ($team === null) {
            return new ViewModel();
        }

        $round = $this->getRoundEntity();
        if ($round === null) {
            return new ViewModel();
        }

        $point = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Point')
            ->findOneBy(
                array(
                    'team'  => $team,
                    'round' => $round,
                )
            );

        if ($point === null) {
            $point = new Point($round, $team, 0);
            $this->getEntityManager()->persist($point);
        }

        $postData = $this->getRequest()->getPost();
        if (isset($postData['score']) && is_numeric($postData['score'])) {
            $point->setPoint($postData['score']);
        } else {
            return new ViewModel(
                array(
                    'result' => array(
                        'status' => 'error',
                    ),
                )
            );
        }

        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function viewAction()
    {
        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $rounds = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Round')
            ->findAllByQuiz($quiz);

        $teams = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Team')
            ->findAllByQuiz($quiz);

        $allPoints = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Point')
            ->findAllByQuiz($quiz);

        $points = array();
        $totals = array();
        foreach ($allPoints as $point) {
            $points[$point->getTeam()->getId()][$point->getRound()->getId()] = $point->getPoint();
            if (!isset($totals[$point->getTeam()->getId()])) {
                $totals[$point->getTeam()->getId()] = 0;
            }
            $totals[$point->getTeam()->getId()] += $point->getPoint();
        }

        arsort($totals);

        $teams_indexed = array();
        foreach ($teams as $team) {
            $teams_indexed[$team->getId()] = $team;
        }

        return new ViewModel(
            array(
                'quiz'         => $quiz,
                'rounds'       => $rounds,
                'teams'        => $teams_indexed,
                'points'       => $points,
                'total_points' => $totals,
                'order'        => $this->getRequest()->getQuery('order', 'ASC'),
            )
        );
    }

    public function resultsAction()
    {
        return $this->viewAction();
    }

    /**
     * @return Person|null
     */
    private function getPersonEntity()
    {
        if (!$this->getAuthentication()->isAuthenticated()) {
            $this->flashMessenger()->error(
                'Error',
                'No user was authenticated!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $this->getAuthentication()->getPersonObject();
    }

    /**
     * @return Quiz|null
     */
    private function getQuizEntity()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $quiz = $this->getEntityById('QuizBundle\Entity\Quiz', 'quizid');

        if (!($quiz instanceof Quiz) || !$quiz->canBeEditedBy($person)) {
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
     * @return Round|null
     */
    private function getRoundEntity()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $round = $this->getEntityById('QuizBundle\Entity\Round', 'roundid');

        if (!($round instanceof Round) || !$round->getQuiz()->canBeEditedBy($person)) {
            $this->flashMessenger()->error(
                'Error',
                'No round was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $round;
    }

    /**
     * @return Team|null
     */
    private function getTeamEntity()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $team = $this->getEntityById('QuizBundle\Entity\Team', 'teamid');

        if (!($team instanceof Team) || !$team->getQuiz()->canBeEditedBy($person)) {
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
