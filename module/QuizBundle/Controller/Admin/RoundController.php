<?php

namespace QuizBundle\Controller\Admin;

use Laminas\View\Model\ViewModel;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\Round;

/**
 * RoundController
 *
 * Controller for /admin/quiz/:quizid/round[/:action[/:id]][/page/:page][/]
 *
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 */
class RoundController extends \CommonBundle\Component\Controller\ActionController\AdminController
{
    public function manageAction()
    {
        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        $paginator = $this->paginator()->createFromEntity(
            'QuizBundle\Entity\Round',
            $this->getParam('page'),
            array(
                'quiz' => $quiz,
            ),
            array(
                'order' => 'ASC',
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

        $form = $this->getForm('quiz_round_add', array('quiz' => $quiz));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $round = $form->hydrateObject(
                    new Round($quiz)
                );

                $this->getEntityManager()->persist($round);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The round was successfully added!'
                );
                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'manage',
                        'quizid' => $quiz->getId(),
                    )
                );
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
        $round = $this->getRoundEntity();
        if ($round === null) {
            return new ViewModel();
        }

        $form = $this->getForm('quiz_round_edit', array('round' => $round));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                $this->flashMessenger()->success(
                    'Success',
                    'The round was successfully edited!'
                );

                $this->redirect()->toRoute(
                    'quiz_admin_round',
                    array(
                        'action' => 'manage',
                        'quizid' => $round->getQuiz()->getId(),
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

        $round = $this->getRoundEntity();
        if ($round === null) {
            return new ViewModel();
        }

        $this->getEntityManager()->remove($round);
        $this->getEntityManager()->flush();

        return new ViewModel(
            array(
                'result' => array(
                    'status' => 'success',
                ),
            )
        );
    }

    public function sortAction()
    {
        $this->initAjax();

        $quiz = $this->getQuizEntity();
        if ($quiz === null) {
            return new ViewModel();
        }

        if (!$this->getRequest()->isPost()) {
            return new ViewModel();
        }

        $data = $this->getRequest()->getPost();

        if (!$data['items']) {
            return new ViewModel();
        }

        foreach ($data['items'] as $order => $id) {
            $round = $this->getEntityManager()->find('QuizBundle\Entity\Round', $id);
            $round->setOrder($order + 1);
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
     * @return Round|null
     */
    private function getRoundEntity()
    {
        $round = $this->getEntityById('QuizBundle\Entity\Round');

        if (!($round instanceof Round) || !$round->getQuiz()->canBeEditedBy($this->getAuthentication()->getPersonObject())) {
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
}
