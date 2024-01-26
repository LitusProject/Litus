<?php

namespace QuizBundle\Controller;

use CommonBundle\Entity\User\Person;
use Laminas\View\Model\ViewModel;
use QuizBundle\Entity\Point;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Round;
use QuizBundle\Entity\Team;
use QuizBundle\Entity\TiebreakerAnswer;

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

        $tiebreaker = $quiz->getTiebreaker();

        if (is_null($tiebreaker)) {
            return new ViewModel(
                array(
                    'quiz'   => $quiz,
                    'rounds' => $rounds,
                    'teams'  => $teams,
                    'points' => $points,
                )
            );
        }

        $allTiebreakersAnswers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\TiebreakerAnswer')
            ->findAllByTiebreaker($tiebreaker);

        $tiebreakerAnswers = array();
        foreach ($allTiebreakersAnswers as $answer) {
            $tiebreakerAnswers[$answer->getTeam()->getId()] = $answer->getAnswer();
        }
        return new ViewModel(
            array(
                'quiz'               => $quiz,
                'rounds'             => $rounds,
                'teams'              => $teams,
                'points'             => $points,
                'tiebreaker'         => $tiebreaker,
                'tiebreaker_answers' => $tiebreakerAnswers,
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

    public function updateTiebreakerAction()
    {
        $this->initAjax();

        $team = $this->getTeamEntity();
        if ($team === null) {
            return new ViewModel();
        }

        $tiebreaker = $this->getQuizEntity()->getTiebreaker();
        if ($tiebreaker === null) {
            return new ViewModel();
        }

        $answer = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\TiebreakerAnswer')
            ->findOneBy(
                array(
                    'team'       => $team,
                    'tiebreaker' => $tiebreaker,
                )
            );

        if ($answer === null) {
            $answer = new TiebreakerAnswer($tiebreaker, $team, 0);
            $this->getEntityManager()->persist($answer);
        }

        $postData = $this->getRequest()->getPost();
        if (isset($postData['answer']) && is_numeric($postData['answer'])) {
            $answer->setAnswer($postData['answer']);
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

        $tiebreaker = $quiz->getTiebreaker();

        $teams_indexed = array();
        foreach ($teams as $team) {
            $teams_indexed[$team->getId()] = $team;
        }

        $points = array(); // [ [teamid][roundid] => point ]
        $totals = array(); // [ [teamid] => totalPoints]
        foreach ($allPoints as $point) {
            $points[$point->getTeam()->getId()][$point->getRound()->getId()] = $point->getPoint();
            if (!isset($totals[$point->getTeam()->getId()])) {
                $totals[$point->getTeam()->getId()] = 0;
            }
            $totals[$point->getTeam()->getId()] += $point->getPoint();
        }
        arsort($totals); // totals sorted by totalPoints

        if (is_null($tiebreaker)) {
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

        $allTiebreakersAnswers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\TiebreakerAnswer')
            ->findAllByTiebreaker($tiebreaker);

        $tiebreakerAnswers = array(); // [ [teamid] => tiebreakeranswer]
        foreach ($allTiebreakersAnswers as $answer) {
            $tiebreakerAnswers[$answer->getTeam()->getId()] = $answer->getAnswer();
        }

        $totals_by_index = array(); // [ [index] => [teamid, totalPoints]
        foreach ($totals as $teamid => $totalPoints) {
            $totals_by_index[] = array($teamid, $totalPoints);
        }


        $totals_with_tiebreaker = array(); // [ [index] => [teamid, totalPoints], tiebreaker considered
        $equal_scores = array(); // Deel van $totals_by_index met dezelfde totalPoints
        for ($i = 0; $i < count($totals_by_index); $i++) {
            error_log($i);
            $equal_scores[] = $totals_by_index[$i];
            if ($i < count($totals_by_index) - 1 && $totals_by_index[$i + 1][1] == $totals_by_index[$i][1]) {
                continue;
                // Zolang er nog elementen in $totals_by_index zitten en de totalPoints gelijk zijn, blijf toevoegen
                // aan $equal_scores
            } else {
                if (count($equal_scores) > 1) {
                    $correctAnswer = $tiebreaker->getCorrectAnswer();

                    // Sorteer equal values op afstand tot correct tiebreakeranswer,
                    //als ze gelijk zijn is de volgorde undefined
                    usort(
                        $equal_scores,
                        fn($a, $b) => abs($correctAnswer - $tiebreakerAnswers[$a[0]]) <=> abs($correctAnswer - $tiebreakerAnswers[$b[0]])
                    );
                }
                foreach ($equal_scores as $score) {
                    $totals_with_tiebreaker[] = $score;
                }
                $equal_scores = array(); //wis equal scores
            }
        }

        $totals_by_teamid = array(); // [ [teamid] => totalPoints] (zelfde als $totals, maar tiebreaker in acht genomen)
        foreach ($totals_with_tiebreaker as $total) {
            $totals_by_teamid[$total[0]] = $total[1];
        }

        return new ViewModel(
            array(
                'quiz'               => $quiz,
                'rounds'             => $rounds,
                'teams'              => $teams_indexed,
                'points'             => $points,
                'total_points'       => $totals_by_teamid,
                'tiebreaker'         => $tiebreaker,
                'tiebreaker_answers' => $tiebreakerAnswers,
                'order'              => $this->getRequest()->getQuery('order', 'ASC'),
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
