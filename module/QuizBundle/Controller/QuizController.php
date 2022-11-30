<?php

namespace QuizBundle\Controller;

use CommonBundle\Entity\User\Person;
use Laminas\View\Model\ViewModel;
use QuizBundle\Entity\Point;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Round;
use QuizBundle\Entity\Team;
use QuizBundle\Entity\Tiebreaker;
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

        $tiebreakers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Tiebreaker')
            ->findAllByQuiz($quiz);

        $allTiebreakersAnswers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\TiebreakerAnswer')
            ->findAllByQuiz($quiz);

        $points = array();
        foreach ($allPoints as $point) {
            $points[$point->getTeam()->getId()][$point->getRound()->getId()] = $point->getPoint();
        }

        $tiebreakerAnswers = array();
        foreach ($allTiebreakersAnswers as $answer) {
            $tiebreakerAnswers[$answer->getTeam()->getId()][$answer->getTiebreaker()->getId()] = $answer->getAnswer();
        }
        return new ViewModel(
            array(
                'quiz' => $quiz,
                'rounds' => $rounds,
                'teams' => $teams,
                'points' => $points,
                'tiebreakers' => $tiebreakers,
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
                    'team' => $team,
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

        $tiebreaker = $this->getTiebreakerEntity();
        if ($tiebreaker === null) {
            return new ViewModel();
        }

        $answer = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\TiebreakerAnswer')
            ->findOneBy(
                array(
                    'team' => $team,
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

        $tiebreakers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\Tiebreaker')
            ->findAllByQuiz($quiz);

        $allTiebreakersAnswers = $this->getEntityManager()
            ->getRepository('QuizBundle\Entity\TiebreakerAnswer')
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

        $tiebreakerAnswers = array();
        foreach ($allTiebreakersAnswers as $answer) {
            $tiebreakerAnswers[$answer->getTeam()->getId()][$answer->getTiebreaker()->getId()] = $answer->getAnswer();
        }

        arsort($totals);
        $totals_by_index = array();
        foreach ($totals as $teamid => $total) {
            $totals_by_index[] = [$teamid, $total];
        }

        $totals_with_tiebreaker = array();
        $equal_scores = array();
        for ($i = 0; $i < count($totals_by_index); $i++) {
            error_log($i);
            $equal_scores[] = $totals_by_index[$i];
            if ($i < count($totals_by_index) - 1 && $totals_by_index[$i + 1][1] == $equal_scores[$i][1]) {
                continue;
            } else {
                if (count($equal_scores) > 1) {
                    foreach ($tiebreakers as $tiebreaker) {
                        $correctAnswer = $tiebreaker->getCorrectAnswer();
                        for ($j = 0; $j < count($equal_scores); $j++) {
                            $equal_scores[$j][1] = abs($correctAnswer - $tiebreakerAnswers[$equal_scores[$j][0]][$tiebreaker->getId()]);
                        }
                        arsort($equal_scores);

                        $point = $totals[$equal_scores[0][0]];
                        $still_equal = false;
                        for ($i = 1; $i < count($equal_scores); $i++) {
                            $teamid = $equal_scores[$i][0];
                            $team_points = $totals[$teamid];
                            if ($team_points == $point) {
                                $still_equal = true;
                                break;
                            } else {
                                $point = $team_points;
                            }
                        }
                        if (!$still_equal) {
                            break;
                        }
                    }
                }
                foreach ($equal_scores as $score) {
                    $score[1] = $totals[$score[0]];
                    $totals_with_tiebreaker[] = $score;
                }
                $equal_scores = array();
            }
        }

        $totals_by_teamid = array();
        foreach ($totals_with_tiebreaker as $total) {
            $totals_by_teamid[$total[0]] = $total[1];
        }

        $teams_indexed = array();
        foreach ($teams as $team) {
            $teams_indexed[$team->getId()] = $team;
        }

        return new ViewModel(
            array(
                'quiz' => $quiz,
                'rounds' => $rounds,
                'teams' => $teams_indexed,
                'points' => $points,
//                'total_points' => $totals,
                'total_points' => $totals_by_teamid,
                'tiebreakers' => $tiebreakers,
                'tiebreaker_answers' => $tiebreakerAnswers,
                'order' => $this->getRequest()->getQuery('order', 'ASC'),
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

    /**
     * @return Tiebreaker|null
     */
    private function getTiebreakerEntity()
    {
        $person = $this->getPersonEntity();
        if ($person === null) {
            return;
        }

        $tiebreaker = $this->getEntityById('QuizBundle\Entity\Tiebreaker', 'roundid');
        if (!($tiebreaker instanceof Tiebreaker) || !$tiebreaker->getQuiz()->canBeEditedBy($person)) {
            $this->flashMessenger()->error(
                'Error',
                'No tiebreaker was found!'
            );

            $this->redirect()->toRoute(
                'quiz_admin_quiz',
                array(
                    'action' => 'manage',
                )
            );

            return;
        }

        return $tiebreaker;
    }
}
